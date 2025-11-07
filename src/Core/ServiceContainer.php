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
	 * @inheritDoc
	 */
	public function bind(string $abstract, mixed $concrete = null, bool $shared = false): void
	{
		unset($this->instances[$abstract]);

		// If no concrete is provided, use the abstract as concrete.
		if ($concrete === null) {
			$concrete = $abstract;
		}

		// Add the binding with its concrete and shared values.
		$this->bindings[$abstract] = compact('concrete', 'shared');
	}

	/**
	 * @inheritDoc
	 */
	public function transient(string $abstract, mixed $concrete = null): void
	{
		$this->bind($abstract, $concrete);
	}

	/**
	 * @inheritDoc
	 */
	public function singleton(string $abstract, mixed $concrete = null): void
	{
		$this->bind($abstract, $concrete, true);
	}

	/**
	 * @inheritDoc
	 */
	public function instance(string $abstract, mixed $instance): void
	{
		$this->instances[$abstract] = $instance;
	}

	/**
	 * @inheritDoc
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
			return $concrete;
		}

		// Build the object.
		$object = $this->build($concrete, $parameters);

		// Cache as an instance if this is a shared/singleton binding
		// and no unique parameters have been passed in.
		if ($this->isShared($abstract) && empty($parameters)) {
			$this->instances[$abstract] = $object;
		}

		return $object;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function make(string $abstract, array $parameters = []): object
	{
		return $this->build($abstract, $parameters);
	}

	/**
	 * @inheritDoc
	 */
	public function has(string $abstract): bool
	{
		return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
	}

	/**
	 * Check if an abstract is bound as a singleton.
	 */
	private function isShared(string $abstract): bool
	{
		return isset($this->bindings[$abstract])
			&& $this->bindings[$abstract]['shared'] === true;
	}

	/**
	 * Determine if the given concrete is buildable.
	 */
	private function isBuildable(mixed $concrete): bool
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
		$reflector = new ReflectionClass($concrete);

		// Get the class constructor method.
		$constructor = $reflector->getConstructor();

		// If there's no constructor, just instantiate.
		if ($constructor === null) {
			return new $concrete();
		}

		// Resolve constructor dependencies and create new instance.
		return $reflector->newInstanceArgs($this->resolveDependencies(
			$constructor->getParameters(),
			$parameters
		));
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

			// Handle anything that is not a named or built-in type.
			if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
				$dependencies[] = $this->resolveNonTyped($param);
				continue;
			}

			// Resolve typed dependency.
			$className = $type->getName();

			// If class is registered with the container, resolve it.
			if ($this->has($className)) {
				$dependencies[] = $this->get($className);
				continue;
			}

			// If the class exists, resolve it.
			if (class_exists($className)) {
				$dependencies[] = $this->make($className);
			}
		}

		return $dependencies;
	}

	/**
	 * Resolve a non-typed or built-in typed parameter.
	 * @throws Exception
	 */
	private function resolveNonTyped(ReflectionParameter $param): mixed
	{
		return $param->isDefaultValueAvailable()
			? $param->getDefaultValue()
			: throw new Exception(esc_html(sprintf(
				// Translators: %s is a parameter name.
				__('Cannot resolve parameter %s.', 'x3p0-breadcrumbs'),
				$param->getName()
			)));
	}
}
