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

final class Archive extends AbstractQuery
{
	/**
	 * {@inheritdoc}
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
		} elseif (get_query_var('minute') && get_query_var('hour')) {
			$this->context->query('minute-hour');
		} elseif (get_query_var('minute')) {
			$this->context->query('minute');
		} elseif (get_query_var('hour')) {
			$this->context->query('hour');
		} elseif (is_day()) {
			$this->context->query('day');
		} elseif (get_query_var('week')) {
			$this->context->query('week');
		} elseif (is_month()) {
			$this->context->query('month');
		} elseif (is_year()) {
			$this->context->query('year');
		} else {
			$this->context->assemble('home');

			// Build rewrite front crumbs if date/time query.
			if (is_date() || is_time()) {
				$this->context->assemble('rewrite-front');
			}

			$this->context->addCrumb('archive');
			$this->context->assemble('paged');
		}
	}
}
