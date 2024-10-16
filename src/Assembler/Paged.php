<?php

/**
 * Paged assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Assemblers out breadcrumbs based on whether we're currently viewing a "paged"
 * page. This handles archive-type pagination, single-post pagination via
 * `<!--nextpage-->`, and comments pagination.
 */
class Paged extends Assembler
{
	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
	{
		// If viewing a paged archive-type page.
		if (is_paged()) {
			$this->builder->addCrumb('paged');

		// If viewing a paged singular post.
		} elseif (is_singular() && 1 < get_query_var('page')) {
			$this->builder->addCrumb('paged-singular');

		// If viewing a singular post with paged comments.
		} elseif (is_singular() && get_option('page_comments') && 1 < get_query_var('cpage')) {
			$this->builder->addCrumb('paged-comments');

		// If viewing a paged Query Loop block view.
		} elseif (Helpers::isPagedQueryBlock()) {
			$this->builder->addCrumb('paged-query-block');
		}
	}
}
