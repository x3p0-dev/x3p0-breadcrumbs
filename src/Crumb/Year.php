<?php

/**
 * Year crumb class.
 *
 * Creates the yearly archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Year extends Base
{
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Post $post = null
	) {}

	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label('archive_year'),
			get_the_time(
				esc_html_x('Y', 'yearly archives date format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	public function url(): string
	{
		return get_year_link(get_the_time('Y', $this->post));
	}
}
