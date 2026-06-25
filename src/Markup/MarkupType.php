<?php

/**
 * Markup type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Canonical keys for the built-in markup formats. The backed string value is
 * the key used in the registry, and the case name maps to the concrete class
 * under the `Type` sub-namespace.
 */
enum MarkupType: string
{
	case Html           = 'html';
	case Microdata      = 'microdata';
	case Rdfa           = 'rdfa';
	case JsonLinkedData = 'json-ld';

	/**
	 * Returns the markup class associated with the type. Each case name matches
	 * a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Markup>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return __NAMESPACE__ . '\\Type\\' . $this->name;
	}
}
