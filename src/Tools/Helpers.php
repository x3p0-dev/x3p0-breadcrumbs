<?php

/**
 * Helpers class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Tools;

use WP_Post_Type;

/**
 * A static class with helper functions for performing some actions needed in
 * the library.
 */
class Helpers
{
	/**
	 * Determines whether we're viewing a paginated page.
	 */
	public static function isPagedView(): bool
	{
		return is_paged()
			|| 1 < absint(get_query_var('page'))
			|| 1 < absint(get_query_var('cpage'))
			|| static::isPagedQueryBlock();
	}

	/**
	 * Determines whether we're viewing a page that has a paginated Query
	 * Loop block.
	 */
	public static function isPagedQueryBlock(): bool
	{
		return static::getQueryBlockPage() > 1;
	}

	/**
	 * Gets the current page number when there's a paginated Query Loop
	 * block. WordPress doesn't have a conditional function for checking
	 * this, and it is not available via `get_query_var()`.
	 */
	public static function getQueryBlockPage(): int
	{
		// Get the URL query for the requested URI.
		$request = isset($_SERVER['REQUEST_URI'])
			? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']))
			: '';

		$query = wp_parse_url($request, PHP_URL_QUERY);

		// Bail early if this is not a paginated page.
		if (
			! $query
			|| ! str_contains($query, 'query-')
			|| ! str_contains($query, 'page=')
		) {
			return 0;
		}

		// Checks for `?query-page={x}` and `query-{x}-page={y}`.
		preg_match('#query-([0-9]\d*-)?page=([0-9]\d*)#i', $query, $matches);

		return isset($matches[2]) ? absint($matches[2]) : 0;
	}

	/**
	 * Gets post types by slug. This is needed because the `get_post_types()`
	 * function doesn't exactly match the `has_archive` argument when it's
	 * set as a string instead of a boolean.
	 *
	 * @return WP_Post_Type[]
	 */
	public static function getPostTypesBySlug(string $slug): array
	{
		$types = [];

		foreach (get_post_types([], 'objects') as $type) {
			if (
				$slug === $type->has_archive
				|| (
					true === $type->has_archive
					&& $slug === $type->rewrite['slug']
				)
			) {
				$types[] = $type;
			}
		}

		return $types;
	}
}
