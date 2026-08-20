<?php

/**
 * Paged comments crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\BreadcrumbsLabel;

/**
 * Crumb for a paginated comments page on a singular post. Labels with the
 * current comment page number and links to that page (with the #comments
 * fragment stripped).
 */
final class PagedComments extends PagedArchive
{
	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'paged-comments';
	}

	/**
	 * {@inheritDoc}
	 *
	 * Comment pages get their own copy, since they're a page of comments
	 * rather than a page of the view itself.
	 */
	protected function labelKey(): BreadcrumbsLabel
	{
		return BreadcrumbsLabel::PagedComments;
	}

	/**
	 * @inheritDoc
	 */
	protected function pageNumber(): int
	{
		return absint(get_query_var('cpage')) ?: 1;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return str_replace('#comments', '', get_comments_pagenum_link(
			$this->pageNumber()
		));
	}
}
