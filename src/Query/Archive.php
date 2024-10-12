<?php

/**
 * Archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

class Archive extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		// Run through the conditionals to determine which type of
		// archive breadcrumbs to build.
		if (is_post_type_archive()) {
			$this->builder->query('post-type-archive');
		} elseif (is_category() || is_tag() || is_tax()) {
			$this->builder->query('taxonomy');
		} elseif (is_author()) {
			$this->builder->query('author');
		} elseif (get_query_var('minute') && get_query_var('hour')) {
			$this->builder->query('minute-hour');
		} elseif (get_query_var('minute')) {
			$this->builder->query('minute');
		} elseif (get_query_var('hour')) {
			$this->builder->query('hour');
		} elseif (is_day()) {
			$this->builder->query('day');
		} elseif (get_query_var('week')) {
			$this->builder->query('week');
		} elseif (is_month()) {
			$this->builder->query('month');
		} elseif (is_year()) {
			$this->builder->query('year');
		} else {
			$this->builder->assemble('home');

			// Build rewrite front crumbs if date/time query.
			if (is_date() || is_time()) {
				$this->builder->assemble('rewrite-front');
			}

			$this->builder->crumb('archive');
			$this->builder->assemble('paged');
		}
	}
}
