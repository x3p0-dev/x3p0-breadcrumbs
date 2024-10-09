<?php

/**
 * Day crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Day extends Crumb
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
			$this->breadcrumbs->label('archive_day'),
			get_the_time(
				esc_html_x('j', 'daily archives date format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return get_day_link(
			get_the_time('Y', $this->post),
			get_the_time('m', $this->post),
			get_the_time('d', $this->post)
		);
	}
}
