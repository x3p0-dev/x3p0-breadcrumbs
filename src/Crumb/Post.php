<?php
/**
 * Post crumb class.
 *
 * Creates the single post crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Post crumb sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class Post extends Base {

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

		$post_id = $this->post->ID;

		if ( is_single( $post_id ) || is_page( $post_id ) || is_attachment( $post_id ) ) {

			return single_post_title( '', false );
		}

		return get_the_title( $this->post->ID );
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function url() {

		return get_permalink( $this->post->ID );
	}
}