<?php

/**
 * Icon option group enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The groups the block editor sorts its icon controls into. An option carries
 * its group's key, and the group's translated label is registered against that
 * key in the `IconOptionRegistry`.
 *
 * These are the built-in groups. Extensions register their own alongside them
 * — WooCommerce gathers its store options under a group of its own — so the set
 * is open, and a group is accepted as `IconOptionGroup|string` on the same
 * terms as {@see IconOptionKey}: a case for this plugin's, a raw string for
 * anyone else's.
 *
 * Only identity lives here. A group's translated label is registered by
 * `IconOptionRegistrar`, which is where every other part of an icon option is
 * assembled too.
 */
enum IconOptionGroup: string
{
	case General         = 'general';
	case PostType        = 'post-types';
	case PostTypeArchive = 'post-type-archives';
	case Taxonomy        = 'taxonomies';
	case Media           = 'media';

	/**
	 * Reduces a group to the string the registry stores it under, passing a
	 * raw string through untouched. The counterpart to
	 * {@see IconOptionKey::normalize()}, called at the same boundaries.
	 */
	public static function normalize(IconOptionGroup|string $group): string
	{
		return $group instanceof self ? $group->value : $group;
	}
}
