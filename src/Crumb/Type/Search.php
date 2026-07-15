<?php

/**
 * Search crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\BreadcrumbsLabel;

/**
 * Crumb representing a search results page. Its label is the configured
 * "search" string filled with the current query, and its URL is the search
 * results link.
 */
final class Search extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf($this->context->config()->getLabel(BreadcrumbsLabel::Search), get_search_query());
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		// `get_search_link()` only encodes the search term, so a search
		// scoped by post type, taxonomy, or any custom query var (e.g.,
		// `?s={search}&post_type={type}`) would lose that scope. Instead,
		// mirror WordPress's own pagination links by capturing the real
		// request URL and resetting it to the first page, which preserves
		// every query variable on the current search results page. The
		// URL is escaped at the point of output, so return it unescaped.
		return is_paged() ? get_pagenum_link(1, false) : get_search_link();
	}
}
