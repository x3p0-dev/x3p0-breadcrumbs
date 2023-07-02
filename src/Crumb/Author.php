<?php
/**
 * Author crumb class.
 *
 * Creates the author archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

class Author extends Base
{
	/**
	 * User object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    \WP_User
	 */
	protected $user;

	/**
	 * Returns a label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string
	{
		return get_the_author_meta( 'display_name', $this->user->ID );
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function url(): string
	{
		return get_author_posts_url( $this->user->ID );
	}
}
