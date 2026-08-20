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

use X3P0\Breadcrumbs\Support\BuildsFromArray;

/**
 * A named icon slot: the unit of icon configuration, defaults, and UI. An
 * option is deliberately not tied to a crumb type — crumbs *consume* options
 * by key (see `Crumb::iconOptionKey()`), several crumbs may share one option
 * (the date archives all pull from `date`), and an option may exist that no
 * built-in crumb uses. Options are collected in the `IconOptionRegistry`.
 *
 * Options with a key derived from a WordPress object have a named constructor
 * apiece — `forPostType()`, `forPostTypeArchive()`, `forTaxonomy()` — so the
 * key, group, and slug are all derived and the option constructed in one call.
 * Options with a flat key of their own (`home`, `separator`, an extension's
 * `woocommerce-cart`) are built with the constructor directly; there is no
 * named alias for it, since nothing about the key needs deriving.
 */
final class IconOption
{
	use BuildsFromArray;

	/**
	 * Keys of the groups the block editor sorts its icon controls into. An
	 * option carries its group's key, and the group's translated label is
	 * registered against that key in the `IconOptionRegistry`. These are the
	 * built-in groups; extensions register their own alongside them, so the
	 * set is open and a plain string — not an enum — is the group's type.
	 */
	public const GROUP_GENERAL = 'general';
	public const GROUP_POST_TYPE = 'post-types';
	public const GROUP_POST_TYPE_ARCHIVE = 'post-type-archives';
	public const GROUP_TAXONOMY = 'taxonomies';
	public const GROUP_MEDIA = 'media';

	/**
	 * The default icon attribute value, normalized to a string. Declared
	 * rather than promoted because the constructor also accepts an {@see Icon}
	 * case for it, which is resolved to its registered name on the way in.
	 */
	public readonly string $icon;

	/**
	 * Sets up the option. The `$key` is the config lookup key (e.g., `home`,
	 * `date`, `post-type:page`, `taxonomy:category`). The `$icon` is the
	 * default icon rendered when the site owner hasn't chosen one, and the
	 * value previewed for the option's block control: either an {@see Icon}
	 * case, for an icon this plugin ships, or a `{collection}/{name}` icon
	 * library reference as a string, for anyone else's — core's `core/home`,
	 * say, which has no case here. An option with a translated `$label` is
	 * offered as a control in the block editor; one without is a pure
	 * default-carrier — resolvable, but invisible in the UI.
	 *
	 * The `$group` is the key of the group the block editor lists the option
	 * under, defaulting to the catch-all. The `$slug` is the bare slug of the
	 * WordPress object the option was derived from, shown beside the label to
	 * tell apart two objects declaring the same one (core's `post_tag` and
	 * WooCommerce's `product_tag` are both "Tag"). The named constructors
	 * below fill in both; an option with a flat key of its own has no object
	 * to name, so its slug stays empty and the editor shows the label alone.
	 * Carrying the slug rather than deriving it means the key namespacing is
	 * never parsed back apart, here or in the editor.
	 */
	public function __construct(
		public readonly string $key,
		Icon|string $icon = '',
		public readonly string $label = '',
		public readonly string $group = self::GROUP_GENERAL,
		public readonly string $slug = ''
	) {
		$this->icon = $icon instanceof Icon ? $icon->name() : $icon;
	}

	/**
	 * Builds the option for a post type's single-post crumbs, keyed by
	 * `postTypeKey()` and grouped with the other post types.
	 */
	public static function forPostType(string $postType, Icon|string $icon = '', string $label = ''): self
	{
		return new self(
			self::postTypeKey($postType),
			$icon,
			$label,
			self::GROUP_POST_TYPE,
			$postType
		);
	}

	/**
	 * Builds the option for a post type's archive crumb, keyed by
	 * `postTypeArchiveKey()` and grouped with the other archives.
	 */
	public static function forPostTypeArchive(string $postType, Icon|string $icon = '', string $label = ''): self
	{
		return new self(
			self::postTypeArchiveKey($postType),
			$icon,
			$label,
			self::GROUP_POST_TYPE_ARCHIVE,
			$postType
		);
	}

	/**
	 * Builds the option for a taxonomy's term crumbs, keyed by `taxonomyKey()`
	 * and grouped with the other taxonomies.
	 */
	public static function forTaxonomy(string $taxonomy, Icon|string $icon = '', string $label = ''): self
	{
		return new self(
			self::taxonomyKey($taxonomy),
			$icon,
			$label,
			self::GROUP_TAXONOMY,
			$taxonomy
		);
	}

	/**
	 * Builds the option key for a post type's single-post crumbs. Consumers
	 * resolving an icon need the key alone (see `Crumb::iconKey()`), so this
	 * stays separate from the `forPostType()` constructor that uses it.
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
