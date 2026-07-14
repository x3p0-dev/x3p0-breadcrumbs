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
	 * Stores the config values as caller overrides only. Built-in defaults
	 * live on `BreadcrumbsLabel` (labels) and in the `mapRewriteTags()`
	 * accessor (which defaults to `true`), so callers pass only what differs.
	 *
	 * @param array<string, bool>   $mapRewriteTags
	 * @param array<string, string> $labels
	 */
	public function __construct(
		private readonly array $mapRewriteTags = [],
		private readonly array $postTaxonomy   = [],
		private readonly array $labels         = [],
		private readonly bool  $network        = false
	) {}

	/**
	 * Returns the label for the given key: a caller override if one is set,
	 * otherwise the built-in default from `BreadcrumbsLabel`. A raw string
	 * key with no override resolves to an empty string.
	 */
	public function getLabel(BreadcrumbsLabel|string $label): string
	{
		$slug = $label instanceof BreadcrumbsLabel ? $label->value : $label;

		return $this->labels[$slug]
			?? ($label instanceof BreadcrumbsLabel ? $label->text() : '');
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
