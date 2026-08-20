<?php

/**
 * Icon option class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * A named icon slot: the unit of icon configuration, defaults, and UI. An
 * option is deliberately not tied to a crumb type — crumbs *consume* options
 * by key (see `Crumb::iconKey()`), several crumbs may share one option (the
 * date archives all pull from `date`), and an option may exist that no
 * built-in crumb uses. Options are collected in the `IconOptions` registry.
 */
final class IconOption
{
	/**
	 * Sets up the option. The `$key` is the config lookup key (e.g., `home`,
	 * `date`, `post-type:page`, `taxonomy:category`). The `$icon` is the
	 * default icon attribute value (e.g., a `{collection}/{name}` icon
	 * library reference) rendered when the site owner hasn't chosen one, and
	 * the value previewed for the option's block control. An option with a
	 * translated `$label` is offered as a control in the block editor; one
	 * without is a pure default-carrier — resolvable, but invisible in the
	 * UI.
	 */
	public function __construct(
		public readonly string $key,
		public readonly string $icon = '',
		public readonly string $label = ''
	) {}

	/**
	 * Builds the option key for a post type's single-post crumbs.
	 */
	public static function postTypeKey(string $postType): string
	{
		return 'post-type:' . $postType;
	}

	/**
	 * Builds the option key for a post type's archive crumb.
	 */
	public static function postTypeArchiveKey(string $postType): string
	{
		return 'post-type-archive:' . $postType;
	}

	/**
	 * Builds the option key for a taxonomy's term crumbs.
	 */
	public static function taxonomyKey(string $taxonomy): string
	{
		return 'taxonomy:' . $taxonomy;
	}
}
