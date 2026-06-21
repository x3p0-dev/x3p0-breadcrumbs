<?php

/**
 * Invalid type exception.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use LogicException;

/**
 * Thrown when a class registered with one of the plugin's registries is not a
 * subclass of that registry's expected base type.
 */
class InvalidTypeException extends LogicException
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
		return new self(sprintf(
			// Translators: 1: given class name, 2: expected base class name.
			__('Cannot register %1$s; only %2$s subclasses are allowed.', 'x3p0-breadcrumbs'),
			esc_html($given),
			esc_html($expected)
		));
	}
}
