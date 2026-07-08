<?php

/**
 * Registration exception.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use LogicException;

/**
 * Thrown when a class cannot be registered with one of the plugin's registries,
 * either because it is not a subclass of the registry's expected base type or
 * because it is not instantiable.
 */
class RegistrationException extends LogicException
{
	/**
	 * Creates an exception for a class that is not a subclass of the expected
	 * base type. Callers are responsible for escaping the message arguments at
	 * the throw site, per the WordPress output-escaping convention.
	 *
	 * @param class-string $given
	 * @param class-string $expected
	 */
	public static function notSubclassOf(string $given, string $expected): self
	{
		return new self(esc_html(sprintf(
			// Translators: 1: given class name, 2: expected base class name.
			__('Cannot register %1$s; only %2$s subclasses are allowed.', 'x3p0-breadcrumbs'),
			$given,
			$expected
		)));
	}

	/**
	 * Creates an exception for a class that cannot be instantiated, such as an
	 * abstract class or one with a non-public constructor. Callers are
	 * responsible for escaping the message arguments at the throw site, per the
	 * WordPress output-escaping convention.
	 *
	 * @param class-string $given
	 */
	public static function notInstantiable(string $given): self
	{
		return new self(esc_html(sprintf(
			// Translators: %s: given class name.
			__('Cannot register %s; the class is not instantiable.', 'x3p0-breadcrumbs'),
			$given
		)));
	}
}
