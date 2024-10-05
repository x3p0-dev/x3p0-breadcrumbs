<?php

/**
 * Helpers class.
 *
 * A static class with helper functions for performing some actions needed in
 * the library.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Util;

class Helpers
{
	/**
	 * Helper function for determining whether we're viewing a paginated page.
	 *
	 * @since 1.0.0
	 */
	public static function isPagedView(): bool
	{
		return is_paged()
			|| 1 < absint(get_query_var('page'))
			|| 1 < absint(get_query_var('cpage'))
			|| static::isPagedQueryBlock();
	}

	/**
	 * Helper function for determining whether we're viewing a page that has
	 * a paginated Query Loop block.
	 *
	 * @since 1.0.0
	 */
	public static function isPagedQueryBlock(): bool
	{
		return static::getQueryBlockPage() > 1;
	}

	/**
	 * Gets the current page number when there's a paginated Query Loop
	 * block. WordPress doesn't have a conditional function for checking
	 * this, and it is not available via `get_query_var()`.
	 *
	 * @since 1.0.0
	 */
	public static function getQueryBlockPage(): int
	{
		// Quick check for `query-page`.
		// False-positive for nonce.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (isset($_GET['query-page'])) {
			return absint(wp_unslash($_GET['query-page']));
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Get the URL query for the requested URI.
		$url_query = parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_QUERY);

		// Bail early if this is not a paginated page.
		if (! $url_query || ! str_contains($url_query, 'page=')) {
			return 0;
		}

		// Queries are based on a specific ID, and there's no surefire
		// way to know what the query ID might be. So, we're checking
		// the URL query for the page here.
		preg_match("#query-[0-9]\d*-page=([0-9]\d*)#i", $url_query, $matches);

		if (isset($matches[0], $matches[1])) {
			return absint($matches[1]);
		}

		return 0;
	}

	/**
	 * Gets post types by slug. This is needed because the `get_post_types()`
	 * function doesn't exactly match the `has_archive` argument when it's
	 * set as a string instead of a boolean.
	 *
	 * @since 1.0.0
	 */
	 public static function getPostTypesBySlug(string $slug): array
	 {
		$return = [];

		$post_types = get_post_types([], 'objects');

		foreach ($post_types as $type) {
			if ($slug === $type->has_archive || (true === $type->has_archive && $slug === $type->rewrite['slug'])) {
				$return[] = $type;
			}
		}

		return $return;
	}
}
