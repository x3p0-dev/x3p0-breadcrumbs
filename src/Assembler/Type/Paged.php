<?php

/**
 * Paged assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Adds a single pagination crumb when the current view is paged. It handles, in
 * priority order, archive-type pagination, single-post pagination via
 * `<!--nextpage-->`, paged comments, and paged Query Loop blocks. At most one
 * crumb is added.
 */
final class Paged extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		// If viewing a paged archive-type page.
		if (is_paged()) {
			$this->context->addCrumb(CrumbType::Paged);

		// If viewing a paged singular post.
		} elseif (is_singular() && 1 < get_query_var('page')) {
			$this->context->addCrumb(CrumbType::PagedSingular);

		// If viewing a singular post with paged comments.
		} elseif (is_singular() && get_option('page_comments') && 1 < get_query_var('cpage')) {
			$this->context->addCrumb(CrumbType::PagedComments);

		// If viewing a paged Query Loop block view.
		} elseif (Helpers::isPagedQueryBlock()) {
			$this->context->addCrumb(CrumbType::PagedQueryBlock);
		}
	}
}
