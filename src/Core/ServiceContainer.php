<?php

/**
 * Container implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Core;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Implementation of the dependency injection container.
 */
final class ServiceContainer implements Container
{
	/**
	 * Stores registered services.
	 */
	protected array $bindings = [];

	/**
	 * Stores registered single-instance services.
	 */
	protected array $instances = [];

	/**
	 * {@inheritDoc}
	 */
	public function bind(string $abstract, mixed $concrete = null, bool $shared = false): void
	{
		// If no concrete is provided, use the abstract as concrete.
		if ($concrete === null) {
			$concrete = $abstract;
		}

		// Wrap string class names in a closure.
		if (is_string($concrete)) {
			$concrete = function (ServiceContainer $container, array $parameters = []) use ($concrete) {
				return $container->make($concrete, $parameters);
			};
		}

		$this->bindings[$abstract] = [
			'concrete' => $concrete,
			'shared'   => $shared,
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function transient(string $abstract, mixed $concrete = null): void
	{
		$this->bind($abstract, $concrete);
	}

	/**
	 * {@inheritDoc}
	 */
	public function singleton(string $abstract, mixed $concrete = null): void
	{
		$this->bind($abstract, $concrete, true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function instance(string $abstract, mixed $instance): void
	{
		$this->instances[$abstract] = $instance;

		$this->bindings[$abstract] = [
			'concrete' => fn() => $instance,
			'shared'   => true,
		];
	}

	/**
	 * {@inheritDoc}
	 * @throws Exception
	 */
	public function get(string $abstract, array $parameters = []): mixed
	{
		// Return cached singleton if exists and no parameters provided.
		if (isset($this->instances[$abstract]) && empty($parameters)) {
			return $this->instances[$abstract];
		}

		// Resolve the binding.
		$concrete = $this->getConcrete($abstract);

		// If we can't build an object, assume we should return the value.
		if (! $this->isBuildable($concrete)) {

			// If we don't actually have this, return false.
			if (! $this->has($abstract)) {
				return false;
			}

			return $concrete;
		}

		// Build the object.
		$object = $this->build($concrete, $parameters);

		// Cache singletons (only if no custom parameters).
		if ($this->isShared($abstract) && empty($parameters)) {
			$this->instances[$abstract] = $object;
		}

		return $object;
	}

	/**
	 * {@inheritDoc}
	 * @throws Exception
	 */
	public function make(string $abstract, array $parameters = []): object
	{
		return $this->build($abstract, $parameters);
	}

	/**
	 * {@inheritDoc}
	 */
	public function has(string $abstract): bool
	{
		return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isShared(string $abstract): bool
	{
		return isset($this->bindings[$abstract])
			&& $this->bindings[$abstract]['shared'] === true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isBuildable(mixed $concrete): bool
	{
		return $concrete instanceof Closure
			|| (is_string($concrete) && class_exists($concrete));
	}

	/**
	 * Get the concrete implementation for an abstract. If no binding
	 * exists, return the abstract itself for auto-resolution.
	 */
	private function getConcrete(string $abstract): mixed
	{
		return ! isset($this->bindings[$abstract])
			? $abstract
			: $this->bindings[$abstract]['concrete'];
	}

	/**
	 * Build an instance of the given concrete.
	 * @throws Exception
	 */
	private function build(Closure|string $concrete, array $parameters = []): object
	{
		// If concrete is a closure, invoke it.
		if ($concrete instanceof Closure) {
			return $concrete($this, $parameters);
		}

		// Otherwise, resolve as a class.
		try {
			$reflector = new ReflectionClass($concrete);
		} catch (ReflectionException $e) {
			throw new Exception(esc_html(sprintf(
			// Translators: %s is a class name.
				__('Target class %s does not exist.', 'x3p0-breadcrumbs'),
				$concrete
			)), 0, $e);
		}

		if (! $reflector->isInstantiable()) {
			throw new Exception(esc_html(sprintf(
			// Translators: %s is a class name.
				__('Target %s is not instantiable.', 'x3p0-breadcrumbs'),
				$concrete
			)));
		}

		$constructor = $reflector->getConstructor();

		// No constructor, just instantiate.
		if ($constructor === null) {
			return new $concrete();
		}

		// Resolve constructor dependencies.
		$dependencies = $this->resolveDependencies(
			$constructor->getParameters(),
			$parameters
		);


		return $reflector->newInstanceArgs($dependencies);
	}

	/**
	 * Resolve constructor dependencies.
	 * @throws Exception
	 */
	private function resolveDependencies(array $params, array $providedParams): array
	{
		$dependencies = [];

		foreach ($params as $param) {
			$name = $param->getName();

			// Use provided parameter if available
			if (array_key_exists($name, $providedParams)) {
				$dependencies[] = $providedParams[$name];
				continue;
			}

			$type = $param->getType();

			// Handle no type hint
			if ($type === null) {
				$dependencies[] = $this->resolveNonTyped($param);
				continue;
			}

			// Handle union types or built-in types
			if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
				$dependencies[] = $this->resolveNonTyped($param);
				continue;
			}

			// Resolve typed dependency
			$className = $type->getName();

			try {
				$dependencies[] = $this->get($className);
			} catch (Exception $e) {
				// If we can't resolve and there's a default, use it
				if ($param->isDefaultValueAvailable()) {
					$dependencies[] = $param->getDefaultValue();
				} else {
					throw $e;
				}
			}
		}

		return $dependencies;
	}

	/**
	 * Resolve a non-typed or built-in typed parameter
	 * @throws Exception
	 */
	private function resolveNonTyped(ReflectionParameter $param): mixed
	{
		if ($param->isDefaultValueAvailable()) {
			return $param->getDefaultValue();
		}

		throw new Exception(esc_html(sprintf(
			// Translators: %s is a parameter name.
			__('Cannot resolve parameter %s.', 'x3p0-breadcrumbs'),
			$param->getName()
		)));
	}
}
