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
 * by key (see `Crumb::iconKey()`), several crumbs may share one option (the
 * date archives all pull from `date`), and an option may exist that no
 * built-in crumb uses. Options are collected in the `IconOptionRegistry`.
 *
 * Options with a key derived from a WordPress object have a named constructor
 * apiece — `postType()`, `postTypeArchive()`, `taxonomy()` — so the key is
 * built and the option constructed in one call. Options with a flat key of
 * their own (`home`, `separator`, an extension's `woocommerce-shop`) are built
 * with the constructor directly; there is no named alias for it, since nothing
 * about the key needs deriving.
 */
final class IconOption
{
	use BuildsFromArray;

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
	 */
	public function __construct(
		public readonly string $key,
		Icon|string $icon = '',
		public readonly string $label = ''
	) {
		$this->icon = $icon instanceof Icon ? $icon->name() : $icon;
	}

	/**
	 * Builds the option for a post type's single-post crumbs, keyed by
	 * `postTypeKey()`.
	 */
	public static function forPostType(string $postType, Icon|string $icon = '', string $label = ''): self
	{
		return new self(self::postTypeKey($postType), $icon, $label);
	}

	/**
	 * Builds the option for a post type's archive crumb, keyed by
	 * `postTypeArchiveKey()`.
	 */
	public static function forPostTypeArchive(string $postType, Icon|string $icon = '', string $label = ''): self
	{
		return new self(self::postTypeArchiveKey($postType), $icon, $label);
	}

	/**
	 * Builds the option for a taxonomy's term crumbs, keyed by
	 * `taxonomyKey()`.
	 */
	public static function forTaxonomy(string $taxonomy, Icon|string $icon = '', string $label = ''): self
	{
		return new self(self::taxonomyKey($taxonomy), $icon, $label);
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
