<?php

/**
 * Label visibility enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Controls which crumbs in the trail render their label, mirroring
 * `IconVisibility`. A crumb's label is only ever hidden when its icon is
 * actually shown (see `Html::isCrumbLabelHidden()`) — a crumb with neither
 * would otherwise render with nothing visible or accessible. Backed by
 * string so a block attribute value can map onto it directly.
 */
enum LabelVisibility: string
{
	/**
	 * Every crumb renders its label.
	 */
	case All = 'all';

	/**
	 * Every crumb except the first renders its label. Common where the
	 * first/home crumb already has an icon standing in for its label (e.g.,
	 * a house icon for "Home").
	 */
	case AllButFirst = 'all_but_first';

	/**
	 * Only the last crumb in the trail renders its label — the rest show
	 * icon only. Common in compact trails where only the current page needs
	 * to be spelled out in text.
	 */
	case Last = 'last';

	/**
	 * No crumb renders its label; the trail is icon-only.
	 */
	case None = 'none';
}
