<?php

/**
 * Archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

class Archive extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		// Run through the conditionals to determine which type of
		// archive breadcrumbs to build.
		if (is_post_type_archive()) {
			$this->breadcrumbs->query('post-type-archive');
		} elseif (is_category() || is_tag() || is_tax()) {
			$this->breadcrumbs->query('taxonomy');
		} elseif (is_author()) {
			$this->breadcrumbs->query('author');
		} elseif (get_query_var('minute') && get_query_var('hour')) {
			$this->breadcrumbs->query('minute-hour');
		} elseif (get_query_var('minute')) {
			$this->breadcrumbs->query('minute');
		} elseif (get_query_var('hour')) {
			$this->breadcrumbs->query('hour');
		} elseif (is_day()) {
			$this->breadcrumbs->query('day');
		} elseif (get_query_var('week')) {
			$this->breadcrumbs->query('week');
		} elseif (is_month()) {
			$this->breadcrumbs->query('month');
		} elseif (is_year()) {
			$this->breadcrumbs->query('year');
		} else {
			$this->breadcrumbs->build('home');

			// Build rewrite front crumbs if date/time query.
			if (is_date() || is_time()) {
				$this->breadcrumbs->build('rewrite-front');
			}

			$this->breadcrumbs->crumb('archive');
			$this->breadcrumbs->build('paged');
		}
	}
}
