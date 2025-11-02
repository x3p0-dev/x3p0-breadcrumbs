<?php

/**
 * Paged assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use X3P0\Breadcrumbs\Assembler\AbstractAssembler;
use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Assemblers out breadcrumbs based on whether we're currently viewing a "paged"
 * page. This handles archive-type pagination, single-post pagination via
 * `<!--nextpage-->`, and comments pagination.
 */
final class Paged extends AbstractAssembler
{
	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		// If viewing a paged archive-type page.
		if (is_paged()) {
			$this->context->addCrumb('paged');

		// If viewing a paged singular post.
		} elseif (is_singular() && 1 < get_query_var('page')) {
			$this->context->addCrumb('paged-singular');

		// If viewing a singular post with paged comments.
		} elseif (is_singular() && get_option('page_comments') && 1 < get_query_var('cpage')) {
			$this->context->addCrumb('paged-comments');

		// If viewing a paged Query Loop block view.
		} elseif (Helpers::isPagedQueryBlock()) {
			$this->context->addCrumb('paged-query-block');
		}
	}
}
