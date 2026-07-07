<?php

/**
 * Pagination support class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\Singleton;

/**
 * Resolves paginated-view state that WordPress does not expose directly, chiefly
 * detecting paginated Query Loop blocks. Marked `#[Singleton]` so the container
 * shares one instance per request and the resolved Query Loop page number is
 * computed only once.
 */
#[Singleton]
final class Pagination
{
	/**
	 * Cached Query Loop block page number for the current request. Remains
	 * `null` until first resolved by `getQueryBlockPage()`; `0` is a valid
	 * resolved value meaning the view is not a paginated Query Loop block.
	 */
	private ?int $queryBlockPage = null;

	/**
	 * Determines whether we're viewing a paginated page.
	 */
	public function isPagedView(): bool
	{
		return is_paged()
			|| 1 < absint(get_query_var('page'))
			|| 1 < absint(get_query_var('cpage'))
			|| $this->isPagedQueryBlock();
	}

	/**
	 * Determines whether we're viewing a page that has a paginated Query
	 * Loop block.
	 */
	public function isPagedQueryBlock(): bool
	{
		return $this->getQueryBlockPage() > 1;
	}

	/**
	 * Gets the current page number when there's a paginated Query Loop
	 * block. WordPress doesn't have a conditional function for checking
	 * this, and it is not available via `get_query_var()`.
	 */
	public function getQueryBlockPage(): int
	{
		// Return the resolved page number if it's already been computed;
		// the URL doesn't change over the course of a request.
		if (null !== $this->queryBlockPage) {
			return $this->queryBlockPage;
		}

		// Get the URL query for the requested URI.
		$query = wp_parse_url(esc_url_raw(add_query_arg([])), PHP_URL_QUERY);

		// Bail early if this is not a paginated page.
		if (
			! $query
			|| ! str_contains($query, 'query-')
			|| ! str_contains($query, 'page=')
		) {
			return $this->queryBlockPage = 0;
		}

		// Checks for `?query-page={x}` and `query-{x}-page={y}`.
		preg_match('#query-(\d+-)?page=(\d+)#', $query, $matches);

		return $this->queryBlockPage = isset($matches[2]) ? absint($matches[2]) : 0;
	}
}
