<?php

/**
 * Resolves a type reference to its registry key.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

/**
 * Shared by the backed `*Type` enums (implementing `ClassEnum`) to normalize a
 * loose type reference to the string key a registry is keyed by. This lets a
 * factory accept an enum case, one of the concrete class names the enum defines,
 * or a bare key string, while its lookup stays a single key-based `get()`.
 *
 * @internal This is an internal implementation detail of the plugin, not part
 *           of its public API. Its signature may change or it may be removed at
 *           any time without notice; third-party code should not use it
 *           directly.
 */
trait ResolvesTypeKey
{
	/**
	 * Normalizes a type reference to its registry key. An enum case yields
	 * its backing value; a class name the enum defines yields that case's
	 * value; any other string is returned unchanged, treated as a key
	 * already (such as a custom one registered by an extension).
	 */
	public static function key(self|string $type): string
	{
		if ($type instanceof self) {
			return $type->value;
		}

		foreach (self::cases() as $case) {
			if ($case->className() === $type) {
				return $case->value;
			}
		}

		return $type;
	}
}
