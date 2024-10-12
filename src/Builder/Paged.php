<?php

/**
 * Paged builder.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Builder;

use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Builders out breadcrumbs based on whether we're currently viewing a "paged"
 * page. This handles archive-type pagination, single-post pagination via
 * `<!--nextpage-->`, and comments pagination.
 */
class Paged extends Builder
{
	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		// If viewing a paged archive-type page.
		if (is_paged()) {
			$this->breadcrumbs->crumb('paged');

		// If viewing a paged singular post.
		} elseif (is_singular() && 1 < get_query_var('page')) {
			$this->breadcrumbs->crumb('paged-singular');

		// If viewing a singular post with paged comments.
		} elseif (is_singular() && get_option('page_comments') && 1 < get_query_var('cpage')) {
			$this->breadcrumbs->crumb('paged-comments');

		// If viewing a paged Query Loop block view.
		} elseif (Helpers::isPagedQueryBlock()) {
			$this->breadcrumbs->crumb('paged-query-block');
		}
	}
}
