<?php

/**
 * Class registry base.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Base class for the plugin's type registries. Stores `string key => class name`
 * mappings that a factory later resolves against, and guards registration so
 * only subclasses of the registry's base type can be stored — guaranteeing every
 * stored value is instantiable as that type. Subclasses declare the base type by
 * returning it from `type()`. Registries are iterable (`key => class-string`) and
 * countable.
 *
 * @internal This is an internal implementation detail of the plugin, not part
 *           of its public API. Its signature may change or it may be removed at
 *           any time without notice; third-party code should not extend or
 *           type-hint against it directly.
 *
 * @template T of object
 * @implements IteratorAggregate<string, class-string<T>>
 */
abstract class ClassRegistry implements IteratorAggregate, Countable
{
	/**
	 * Maps each registered key to its class name.
	 *
	 * @var array<string, class-string<T>>
	 */
	private array $classes = [];

	/**
	 * Returns the base class name that every registered class must be a
	 * subclass of.
	 *
	 * @return class-string<T>
	 */
	abstract protected function type(): string;

	/**
	 * Optionally seeds the registry with an initial `key => class` map.
	 *
	 * @param array<string, class-string<T>> $classes
	 */
	public function __construct(array $classes = [])
	{
		foreach ($classes as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Maps `$key` to `$className`, overwriting any existing mapping. Throws
	 * if `$className` is not a subclass of the registry's base type.
	 *
	 * @param class-string<T> $className
	 */
	public function register(string $key, string $className): void
	{
		if (! is_subclass_of($className, $this->type())) {
			throw InvalidTypeException::notSubclassOf(
				esc_html($className),
				esc_html($this->type())
			);
		}

		$this->classes[$key] = $className;
	}

	/**
	 * Removes the mapping for `$key`, if any.
	 */
	public function unregister(string $key): void
	{
		unset($this->classes[$key]);
	}

	/**
	 * Returns whether a class is registered under `$key`.
	 */
	public function isRegistered(string $key): bool
	{
		return array_key_exists($key, $this->classes);
	}

	/**
	 * Returns the class name registered under `$key`, or `null` if none.
	 *
	 * @return null|class-string<T>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->classes[$key] : null;
	}

	/**
	 * Returns every registered key mapped to its class name, including any
	 * registered by third-party code.
	 *
	 * @return array<string, class-string<T>>
	 */
	public function all(): array
	{
		return $this->classes;
	}

	/**
	 * @inheritDoc
	 *
	 * @return ArrayIterator<string, class-string<T>>
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->classes);
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int
	{
		return count($this->classes);
	}
}
