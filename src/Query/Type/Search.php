<?php

/**
 * Search query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\{Query, QueryRegistrar};

final class Search extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		// If this is also a post type archive, forward to the post type
		// archive query, which will handle post type + search queries.
		if (is_post_type_archive()) {
			$this->context->query(QueryRegistrar::POST_TYPE_ARCHIVE);
			return;
		}

		$this->context->assemble(AssemblerType::Home);
		$this->context->assemble(AssemblerType::RewriteFront);
		$this->context->addCrumb(CrumbType::Search);
		$this->context->assemble(AssemblerType::Paged);
	}
}
