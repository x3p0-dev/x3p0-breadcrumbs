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
 * Builds an immutable value object from an associative array, passing only the
 * keys that match a constructor parameter as named arguments and ignoring the
 * rest. The parameter list is derived from the constructor via reflection so it
 * cannot drift from the signature.
 *
 * @internal This is an internal implementation detail of the plugin, not part
 *           of its public API. Its signature may change or it may be removed at
 *           any time without notice; third-party code should not use it
 *           directly.
 */
trait BuildsFromArray
{
	/**
	 * Builds an instance from an associative array, ignoring any keys that
	 * do not map to a constructor parameter.
	 */
	public static function fromArray(array $options = []): static
	{
		static $paramNames;

		$paramNames ??= array_column(
			(new ReflectionMethod(static::class, '__construct'))->getParameters(),
			'name'
		);

		return new static(...array_intersect_key($options, array_flip($paramNames)));
	}
}
