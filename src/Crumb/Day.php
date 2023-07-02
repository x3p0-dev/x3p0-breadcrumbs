<?php
/**
 * Day crumb class.
 *
 * Creates the day archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

class Day extends Base
{
	/**
	 * Post object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    \WP_Post
	 */
	protected $post = null;

	/**
	 * Returns a label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label( 'archive_day' ),
			get_the_time(
				esc_html_x( 'j', 'daily archives date format', 'x3p0-breadcrumbs' ),
				$this->post
			)
		);
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function url(): string
	{
		return get_day_link(
			get_the_time( 'Y', $this->post ),
			get_the_time( 'm', $this->post ),
			get_the_time( 'd', $this->post )
		);
	}
}
