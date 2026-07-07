<?php

/**
 * Helpers class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Tools;

use WP_Post_Type;

/**
 * A collection of stateless helper methods for request and post-type checks
 * that WordPress does not expose directly — chiefly detecting paginated views
 * (including paginated Query Loop blocks) and resolving post types by archive
 * slug.
 */
final class Helpers
{
	/**
	 * Lazily built map of archive slug to the post type objects registered
	 * under it, cached for the duration of the request. Remains `null`
	 * until first built by `getPostTypesBySlug()`.
	 *
	 * @var null|array<string, WP_Post_Type[]>
	 */
	private static ?array $postTypesBySlug = null;

	/**
	 * Determines whether we're viewing a paginated page.
	 */
	public static function isPagedView(): bool
	{
		return is_paged()
			|| 1 < absint(get_query_var('page'))
			|| 1 < absint(get_query_var('cpage'))
			|| self::isPagedQueryBlock();
	}

	/**
	 * Determines whether we're viewing a page that has a paginated Query
	 * Loop block.
	 */
	public static function isPagedQueryBlock(): bool
	{
		return self::getQueryBlockPage() > 1;
	}

	/**
	 * Gets the current page number when there's a paginated Query Loop
	 * block. WordPress doesn't have a conditional function for checking
	 * this, and it is not available via `get_query_var()`.
	 */
	public static function getQueryBlockPage(): int
	{
		// Get the URL query for the requested URI.
		$query = wp_parse_url(esc_url_raw(add_query_arg([])), PHP_URL_QUERY);

		// Bail early if this is not a paginated page.
		if (
			! $query
			|| ! str_contains($query, 'query-')
			|| ! str_contains($query, 'page=')
		) {
			return 0;
		}

		// Checks for `?query-page={x}` and `query-{x}-page={y}`.
		preg_match('#query-(\d+-)?page=(\d+)#', $query, $matches);

		return isset($matches[2]) ? absint($matches[2]) : 0;
	}

	/**
	 * Gets post types by slug. This is needed because the `get_post_types()`
	 * function doesn't exactly match the `has_archive` argument when it's
	 * set as a string instead of a boolean.
	 *
	 * The archive-slug lookup is built once and cached, so repeated
	 * resolutions (e.g. walking a URL path segment by segment) don't rescan
	 * the post type registry each time.
	 *
	 * @return WP_Post_Type[]
	 */
	public static function getPostTypesBySlug(string $slug): array
	{
		return self::archiveSlugMap()[$slug] ?? [];
	}

	/**
	 * Builds (once) and returns the map of archive slug to the post type
	 * objects registered under it. A string `has_archive` is itself the
	 * archive slug; a boolean-true `has_archive` falls back to the rewrite
	 * slug. A `false` `has_archive` (or a disabled rewrite) contributes no
	 * entry.
	 *
	 * @return array<string, WP_Post_Type[]>
	 */
	private static function archiveSlugMap(): array
	{
		if (null !== self::$postTypesBySlug) {
			return self::$postTypesBySlug;
		}

		self::$postTypesBySlug = [];

		foreach (get_post_types([], 'objects') as $type) {
			if (is_string($type->has_archive)) {
				self::$postTypesBySlug[$type->has_archive][] = $type;
			} elseif (
				true === $type->has_archive
				&& is_array($type->rewrite)
				&& isset($type->rewrite['slug'])
			) {
				self::$postTypesBySlug[$type->rewrite['slug']][] = $type;
			}
		}

		return self::$postTypesBySlug;
	}
}
