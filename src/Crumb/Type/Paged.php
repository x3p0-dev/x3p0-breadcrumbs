<?php

/**
 * Paged crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

/**
 * Crumb for a paginated archive page. Labels with the current page number and
 * links to that page's URL.
 */
final class Paged extends PagedArchive
{
	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'paged';
	}

	/**
	 * @inheritDoc
	 */
	protected function pageNumber(): int
	{
		return absint(get_query_var('paged')) ?: 1;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return get_pagenum_link($this->pageNumber());
	}
}
