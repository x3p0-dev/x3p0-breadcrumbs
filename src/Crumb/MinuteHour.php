<?php
/**
 * Minute + Hour crumb class.
 *
 * Creates the minute + hour archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Minute + Hour crumb sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class MinuteHour extends Base {

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
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function label() {

		return sprintf(
			$this->breadcrumbs->label( 'archive_minute_hour' ),
			get_the_time(
				esc_html_x( 'g:i a', 'minute and hour archives time format', 'x3p0-breadcrumbs' ),
				$this->post
			)
		);
	}
}
