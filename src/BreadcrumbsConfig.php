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
	 * Whether each post type's permalink rewrite tags map into crumbs, keyed
	 * by post type. Caller values are merged over the built-in defaults.
	 *
	 * @var array<string, bool>
	 */
	private readonly array $mapRewriteTags;

	/**
	 * Stores the config values. Labels are kept as caller overrides only
	 * (defaults live on `BreadcrumbsLabel`), while rewrite-tag settings are
	 * merged on top of the built-in defaults so callers only override what
	 * differs.
	 *
	 * @param array<string, string> $labels
	 */
	public function __construct(
		array                  $mapRewriteTags = [],
		private readonly array $postTaxonomy   = [],
		private readonly array $labels         = [],
		private readonly bool  $network        = false
	) {
		$this->mapRewriteTags = array_merge($this->defaultRewriteTags(), $mapRewriteTags);
	}

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

	/**
	 * Returns an array of the default post rewrite tag settings. Array keys
	 * should be the post type and array values a boolean that sets whether
	 * the rewrite tags should be mapped for the permalink structure as
	 * breadcrumbs.
	 */
	private function defaultRewriteTags(): array
	{
		$types = array_filter(
			get_post_types(['publicly_queryable' => true], 'objects'),
			fn($type) => is_array($type->rewrite) && str_contains($type->rewrite['slug'] ?? '', '%')
		);

		return ['post' => true] + array_fill_keys(array_keys($types), true);
	}
}
