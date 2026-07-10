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

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Crumb for a paginated comments page on a singular post. Labels with the
 * current comment page number and links to that page (with the #comments
 * fragment stripped).
 */
final class PagedComments extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->context->config()->getLabel('paged_comments'),
			number_format_i18n(absint(get_query_var('cpage')))
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return str_replace('#comments', '', get_comments_pagenum_link(
			get_query_var('cpage') ? absint(get_query_var('cpage')) : 1
		));
	}
}
