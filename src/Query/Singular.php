<?php
/**
 * Singular query class.
 *
 * Called to build breadcrumbs on singular posts.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Query;

/**
 * Singular query sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class Singular extends Base {

	/**
	 * Post object.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    \WP_Post
	 */
	protected $post;

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function make() {

		$post = $this->post ?: get_queried_object();

		// Build network crumbs.
		$this->breadcrumbs->build( 'Network' );

		// Add site home crumb.
		$this->breadcrumbs->crumb( 'Home' );

		// Build post crumbs.
		$this->breadcrumbs->build( 'Post', [ 'post' => $post ] );

		// Add post crumb.
		$this->breadcrumbs->crumb( 'Post', [ 'post' => $post ] );

		// Build paged crumbs.
		$this->breadcrumbs->build( 'Paged' );
	}
}
