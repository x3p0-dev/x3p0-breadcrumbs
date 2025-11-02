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

use X3P0\Breadcrumbs\Query\AbstractQuery;
use X3P0\Breadcrumbs\Tools\Helpers;

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
			$this->context->query('post-type-archive');
		} elseif (is_category() || is_tag() || is_tax()) {
			$this->context->query('taxonomy');
		} elseif (is_author()) {
			$this->context->query('author');
		} elseif (is_date()) {
			$this->context->query('date');
		} else {
			$this->context->assemble('home');
			$this->context->addCrumb('archive');
			$this->context->assemble('paged');
		}
	}
}
