<?php

/**
 * Minute + Hour crumb class.
 *
 * Creates the minute + hour archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class MinuteHour extends Crumb
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Post $post = null
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label('archive_minute_hour'),
			get_the_time(
				esc_html_x('g:i a', 'minute and hour archives time format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}
}
