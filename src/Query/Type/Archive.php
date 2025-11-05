<?php

/**
 * Archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use X3P0\Breadcrumbs\Assembler\AssemblerRegistrar;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;
use X3P0\Breadcrumbs\Query\{AbstractQuery, QueryRegistrar};

final class Archive extends AbstractQuery
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		// Run through the conditionals to determine which type of
		// archive breadcrumbs to build.
		if (is_post_type_archive()) {
			$this->context->query(QueryRegistrar::POST_TYPE_ARCHIVE);
		} elseif (is_category() || is_tag() || is_tax()) {
			$this->context->query(QueryRegistrar::TAXONOMY);
		} elseif (is_author()) {
			$this->context->query(QueryRegistrar::AUTHOR);
		} elseif (is_date()) {
			$this->context->query(QueryRegistrar::DATE);
		} else {
			$this->context->assemble(AssemblerRegistrar::HOME);
			$this->context->addCrumb(CrumbRegistrar::ARCHIVE);
			$this->context->assemble(AssemblerRegistrar::PAGED);
		}
	}
}
