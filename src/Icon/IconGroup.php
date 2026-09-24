<?php

/**
 * Icon group enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The built-in groups the block editor sorts its icon controls under. A group
 * is a key and the translated heading it is listed by.
 */
enum IconGroup: string
{
	case General         = 'general';
	case PostType        = 'post-types';
	case PostTypeArchive = 'post-type-archives';
	case Taxonomy        = 'taxonomies';
	case Media           = 'media';

	/**
	 * Returns the group's translated heading.
	 */
	public function label(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::General         => __('General', 'x3p0-breadcrumbs'),
			self::PostType        => __('Post Types', 'x3p0-breadcrumbs'),
			self::PostTypeArchive => __('Post Type Archives', 'x3p0-breadcrumbs'),
			self::Taxonomy        => __('Taxonomies', 'x3p0-breadcrumbs'),
			self::Media           => __('Media', 'x3p0-breadcrumbs')
		};
	}

	/**
	 * Reduces a group to the string it is keyed by, passing a raw string
	 * through untouched.
	 */
	public static function normalize(IconGroup|string $group): string
	{
		return $group instanceof self ? $group->value : $group;
	}
}
