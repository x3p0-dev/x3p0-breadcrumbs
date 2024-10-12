<?php

/**
 * Week crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

class Week extends Crumb
{
	/**
	 * {@inheritdoc}
	 */
	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label('archive_week'),
			get_the_time(esc_html_x(
				'W',
				'weekly archives date format',
				'x3p0-breadcrumbs'
			))
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return add_query_arg([
			'm' => get_the_time('Y'),
			'w' => get_the_time('W')
		], user_trailingslashit(home_url()));
	}
}
