<?php

/**
 * Icon visibility enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Controls which crumbs in the trail render their icon. Independent of
 * whether a crumb actually has an icon configured (see `Crumb::getIcon()`,
 * which always resolves to a value) — this is purely a show/hide switch.
 * Backed by string so a block attribute value can map onto it directly.
 */
enum IconVisibility: string
{
	/**
	 * No crumb renders its icon.
	 */
	case None = 'none';

	/**
	 * Only the first crumb in the trail renders its icon.
	 */
	case First = 'first';

	/**
	 * Every crumb except the last renders its icon. Common where the last
	 * crumb is the current page.
	 */
	case AllButLast = 'all-but-last';

	/**
	 * Every crumb in the trail renders its icon.
	 */
	case All = 'all';

	/**
	 * Returns the default, translated label text for the case, used as the
	 * block editor's select control option label.
	 */
	public function label(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::None       => __('None', 'x3p0-breadcrumbs'),
			self::First      => __('First Crumb Only', 'x3p0-breadcrumbs'),
			self::AllButLast => __('All Except Last', 'x3p0-breadcrumbs'),
			self::All        => __('All Crumbs', 'x3p0-breadcrumbs')
		};
	}
}
