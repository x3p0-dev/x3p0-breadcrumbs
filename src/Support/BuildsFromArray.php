<?php

/**
 * Builds a value object from an array.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

use ReflectionMethod;

/**
 * Derives immutable value objects from a parameter map: `fromArray()` builds one
 * from an associative array, and `with()` returns a copy of an existing instance
 * with one or more options changed. Both map to the constructor parameters,
 * derived via reflection so they cannot drift from the signature. Assumes the
 * parameters are promoted properties of the same name (as the plugin's config
 * objects are), so `with()` can read the current values back.
 *
 * @internal The trait itself is an internal implementation detail; third-party
 *           code should not use it directly. Its public methods, once composed
 *           into a class, are part of that class's public API.
 */
trait BuildsFromArray
{
	/**
	 * Builds an instance from an associative array, ignoring any keys that
	 * do not map to a constructor parameter.
	 */
	public static function fromArray(array $options = []): static
	{
		return new static(...array_intersect_key(
			$options,
			array_flip(self::constructorParamNames())
		));
	}

	/**
	 * Returns a copy of this instance with the given options overridden,
	 * leaving every other option carried through from the current values.
	 * The instance stays immutable — a new one is constructed rather than
	 * mutated. Throws if any key does not map to a constructor parameter.
	 *
	 * @param array<string, mixed> $options
	 */
	public function with(array $options): static
	{
		$current = [];

		foreach (self::constructorParamNames() as $name) {
			$current[$name] = $this->$name;
		}

		return new static(...array_merge($current, $options));
	}

	/**
	 * Returns the constructor's parameter names, cached per class. Drives
	 * both `fromArray()` and `with()` so their parameter mapping cannot
	 * diverge.
	 *
	 * @return list<string>
	 */
	private static function constructorParamNames(): array
	{
		static $paramNames;

		return $paramNames ??= array_column(
			(new ReflectionMethod(static::class, '__construct'))->getParameters(),
			'name'
		);
	}
}
