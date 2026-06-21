<?php

/**
 * Date assembler.
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

/**
 * Assembles breadcrumbs for date/time archives based on the current query.
 * Supports year, month, week, day, hour, minute, and second granularity, as
 * well as the `?m=YYYYMMDDHHMMSS` plain-permalink format.
 */
final class Date extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		[
			'year'   => $year,
			'month'  => $month,
			'day'    => $day,
			'hour'   => $hour,
			'minute' => $minute,
			'second' => $second
		] = $this->getQueryStringVars();

		if (is_year() || get_query_var('year') || $year) {
			$this->context->addCrumb(CrumbType::Year);
		}

		if (is_month() || get_query_var('monthnum') || $month) {
			$this->context->addCrumb(CrumbType::Month);
		}

		if (get_query_var('w')) {
			$this->context->addCrumb(CrumbType::Week);
		}

		if (is_day() || get_query_var('day') || $day) {
			$this->context->addCrumb(CrumbType::Day);
		}

		if (get_query_var('hour') || $hour) {
			$this->context->addCrumb(CrumbType::Hour);
		}

		if (get_query_var('minute') || $minute) {
			$this->context->addCrumb(CrumbType::Minute);
		}

		if (get_query_var('second') || $second) {
			$this->context->addCrumb(CrumbType::Second);
		}
	}

	/**
	 * Parses the `?m=YYYYMMDDHHMMSS` query var used with plain permalinks
	 * and maps each substring to its named date component. Returns `false`
	 * for every component when pretty permalinks are active or `?m` is unset.
	 */
	private function getQueryStringVars(): array
	{
		$parts = [
			'year'   => [ 0, 4],
			'month'  => [ 4, 2],
			'day'    => [ 6, 2],
			'hour'   => [ 8, 2],
			'minute' => [10, 2],
			'second' => [12, 2]
		];

		if (get_option('permalink_structure') || ! get_query_var('m')) {
			return array_fill_keys(array_keys($parts), false);
		}

		$dateString = preg_replace('/[^0-9]/', '', get_query_var('m'));

		$vars   = [];
		$length = strlen($dateString);

		foreach ($parts as $key => [$start, $len]) {
			$vars[$key] = ($start + $len <= $length)
				? substr($dateString, $start, $len)
				: false;
		}

		return $vars;
	}
}
