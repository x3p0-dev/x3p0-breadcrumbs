<?php

/**
 * Markup option interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Implemented by markup types that may be presented as a selectable option,
 * such as the block editor's markup-style control. Types that opt in supply
 * the human-readable label shown for the choice; types that do not implement
 * this contract (e.g., JSON-LD) are omitted from those controls.
 */
interface MarkupOption
{
	/**
	 * Returns the internationalized label shown when presenting this markup
	 * type as a selectable option.
	 */
	public static function label(): string;
}
