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

use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;
use X3P0\Breadcrumbs\Query\QueryType;

/**
 * Dispatcher for archive requests. It inspects the request and forwards to the
 * more specific post type, taxonomy, author, or date query. When none of those
 * apply, it builds a generic archive trail directly.
 */
final class Archive extends Query
{
	/**
	 * {@inheritDoc}
	 *
	 * Forwards to the matching specialized query, or, as a fallback, adds the
	 * home, a generic archive crumb, and paged steps.
	 */
	public function query(): void
	{
		if (is_post_type_archive()) {
			$this->context->query(QueryType::PostTypeArchive);
		} elseif (is_category() || is_tag() || is_tax()) {
			$this->context->query(QueryType::Taxonomy);
		} elseif (is_author()) {
			$this->context->query(QueryType::Author);
		} elseif (is_date()) {
			$this->context->query(QueryType::Date);
		} else {
			$this->context->assemble(AssemblerType::Home);
			$this->context->addCrumb(CrumbType::Archive);
			$this->context->assemble(AssemblerType::Paged);
		}
	}
}
