<?php
/**
 * Year crumb class.
 *
 * Creates the yearly archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

class Year extends Base
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
			$this->breadcrumbs->label( 'archive_year' ),
			get_the_time(
				esc_html_x( 'Y', 'yearly archives date format', 'x3p0-breadcrumbs' ),
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
		return get_year_link( get_the_time( 'Y', $this->post ) );
	}
}
