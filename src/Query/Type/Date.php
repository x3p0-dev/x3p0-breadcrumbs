<?php

/**
 * Date (and time) query class.
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

final class Date extends AbstractQuery
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble('home');
		$this->context->assemble('rewrite-front');

		[
			'year'   => $year,
			'month'  => $month,
			'day'    => $day,
			'hour'   => $hour,
			'minute' => $minute,
			'second' => $second
		] = $this->getQueryStringDate();

		if (is_year() || get_query_var('year') || $year) {
			$this->context->addCrumb('year');
		}

		if (is_month() || get_query_var('monthnum') || $month) {
			$this->context->addCrumb('month');
		}

		if (get_query_var('w')) {
			$this->context->addCrumb('week');
		}

		if (is_day() || get_query_var('day') || $day) {
			$this->context->addCrumb('day');
		}

		if (get_query_var('hour') || $hour) {
			$this->context->addCrumb('hour');
		}

		if (get_query_var('minute') || $minute) {
			$this->context->addCrumb('minute');
		}

		if (get_query_var('second') || $second) {
			$this->context->addCrumb('second');
		}

		$this->context->assemble('paged');
	}

	/**
	 * Helper function for parsing date URLs when using plain permalinks
	 * like: `?m=YYYYMMDDHHMMSS`
	 */
	private function getQueryStringDate(): array
	{
		// Define mapping of component lengths and positions.
		$components = [
			'year'   => [0, 4],
			'month'  => [4, 2],
			'day'    => [6, 2],
			'hour'   => [8, 2],
			'minute' => [10, 2],
			'second' => [12, 2]
		];

		// Bail early, returning `false` for each array item.
		if (! get_option('permalink_structure') || ! get_query_var('m')) {
			return array_fill_keys(array_keys($components), false);
		}

		// Get from query var and remove non-numeric characters.
		$dateString = preg_replace('/[^0-9]/', '', get_query_var('m'));

		$result = [];
		$length = strlen($dateString);

		foreach ($components as $key => [$start, $len]) {
			$result[$key] = ($start + $len <= $length)
				? substr($dateString, $start, $len)
				: false;
		}

		return $result;
	}
}
