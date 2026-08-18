<?php

/**
 * Breadcrumbs configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Support\BuildsFromArray;

/**
 * Immutable configuration object passed through the breadcrumbs pipeline. It
 * controls how the trail is built: the labels used for generated crumbs, which
 * taxonomy represents a given post type, whether a post type's permalink
 * rewrite tags are mapped into crumbs, and whether the network (multisite)
 * crumb is shown.
 */
final class BreadcrumbsConfig
{
	use BuildsFromArray;

	/**
	 * Built-in icon defaults for WordPress's own core post types, used by
	 * `getPostTypeIcon()`/`getPostTypeArchiveIcon()` beneath any caller
	 * override. Each entry may set a `single` icon (the post type's single-post
	 * crumb), an `archive` icon (its post type archive crumb), or both. `post`
	 * isn't listed here because it already resolves through
	 * `Crumb\Type\Post::DEFAULT_ICON`; `page` gets its own `single` entry since
	 * no core icon distinguishes it from a generic post (pages have no
	 * archive). `attachment`'s generic `single` default is superseded by a
	 * media-type-aware icon (image, audio) resolved in `Post::getIcon()` where
	 * a matching one exists.
	 *
	 * @var  array<string, array{single?: string, archive?: string}>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const DEFAULT_POST_TYPE_ICONS = [
		'attachment' => ['single' => 'core/file'],
		'page'       => ['single' => 'x3p0-breadcrumbs/article'],
		'product'    => ['single' => 'x3p0-breadcrumbs/package']
	];

	/**
	 * Built-in icon defaults for WordPress's own core taxonomies, used by
	 * `getTaxonomyIcon()` beneath any caller override.
	 *
	 * @var  array<string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const DEFAULT_TAXONOMY_ICONS = [
		'category'    => 'core/category',
		'post_tag'    => 'core/tag',
		'product_cat' => 'core/category',
		'product_tag' => 'core/tag',
	];

	/**
	 * Stores the config values as caller overrides only. Built-in defaults
	 * live on `BreadcrumbsLabel` (labels) and in the `mapRewriteTags()`
	 * accessor (which defaults to `true`), so callers pass only what differs.
	 *
	 * @param array<string, bool>                                      $mapRewriteTags
	 * @param array<string, string>                                    $labels
	 * @param array<string, string>                                    $icons
	 * @param array<string, array{single?: string, archive?: string}>  $postTypeIcons
	 * @param array<string, string>                                    $taxonomyIcons
	 */
	public function __construct(
		private readonly array $mapRewriteTags = [],
		private readonly array $postTaxonomy   = [],
		private readonly array $labels         = [],
		private readonly array $icons          = [],
		private readonly array $postTypeIcons  = [],
		private readonly array $taxonomyIcons  = [],
		private readonly bool  $network        = false
	) {}

	/**
	 * Returns the label for the given key: a caller override if one is set,
	 * otherwise the built-in default from `BreadcrumbsLabel`. A raw string
	 * that does not match one of the enum's values resolves to an empty
	 * string.
	 */
	public function getLabel(BreadcrumbsLabel|string $label): string
	{
		$label = is_string($label) ? BreadcrumbsLabel::tryFrom($label) : $label;

		if (! $label instanceof BreadcrumbsLabel) {
			return '';
		}

		return $this->labels[$label->value] ?? $label->text();
	}

	/**
	 * Returns the taxonomy mapped to the given post type for building its
	 * crumbs, or an empty string if none is mapped.
	 */
	public function getPostTaxonomy(string $postType): string
	{
		return $this->postTaxonomy[$postType] ?? '';
	}

	/**
	 * Returns the icon attribute value configured for the given crumb slug
	 * (e.g., `home`, `author`, `search`), or an empty string if none is
	 * configured. Left for the crumb/`Markup` layers to resolve to real
	 * markup and to fall back to a default when empty.
	 */
	public function getIcon(string $slug): string
	{
		return $this->icons[$slug] ?? '';
	}

	/**
	 * Returns the icon attribute value for the given post type's single-post
	 * crumb: a caller override if one is set, otherwise the built-in default
	 * from `DEFAULT_POST_TYPE_ICONS`, otherwise an empty string.
	 */
	public function getPostTypeIcon(string $postType): string
	{
		return $this->postTypeIcons[$postType]['single']
			?? self::DEFAULT_POST_TYPE_ICONS[$postType]['single']
			?? '';
	}

	/**
	 * Returns the icon attribute value for the given post type's archive
	 * crumb: a caller override if one is set, otherwise the built-in default
	 * from `DEFAULT_POST_TYPE_ICONS`, otherwise an empty string.
	 */
	public function getPostTypeArchiveIcon(string $postType): string
	{
		return $this->postTypeIcons[$postType]['archive']
			?? self::DEFAULT_POST_TYPE_ICONS[$postType]['archive']
			?? '';
	}

	/**
	 * Returns the icon attribute value for the given taxonomy: a caller
	 * override if one is set, otherwise the built-in default from
	 * `DEFAULT_TAXONOMY_ICONS`, otherwise an empty string.
	 */
	public function getTaxonomyIcon(string $taxonomy): string
	{
		return $this->taxonomyIcons[$taxonomy] ?? self::DEFAULT_TAXONOMY_ICONS[$taxonomy] ?? '';
	}

	/**
	 * Determines whether the given post type's permalink rewrite tags should
	 * be mapped into crumbs. Defaults to `true` for post types without an
	 * explicit setting.
	 */
	public function mapRewriteTags(string $postType): bool
	{
		return $this->mapRewriteTags[$postType] ?? true;
	}

	/**
	 * Determines whether to show the network crumb for multisite installs.
	 */
	public function showNetwork(): bool
	{
		return $this->network;
	}
}
