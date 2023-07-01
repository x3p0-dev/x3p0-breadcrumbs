<?php
/**
 * Post build class.
 *
 * This is a wrapper to determine a more specific post-related build class to
 * call based on the given post.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Build;

/**
 * Post build sub-class.
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
	protected $post;

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function make() {

		// If the post has a parent, follow the parent trail.
		if ( 0 < $this->post->post_parent ) {

			$this->breadcrumbs->build( 'PostAncestors', [
				'post' => $this->post
			] );

		// If the post doesn't have a parent, get its hierarchy based off the post type.
		} else {

			$this->breadcrumbs->build( 'PostHierarchy', [
				'post' => $this->post
			] );
		}

		// Display terms for specific post type taxonomy if requested.
		if ( $this->breadcrumbs->postTaxonomy( $this->post->post_type ) ) {

			$this->breadcrumbs->build( 'PostTerms', [
				'post'     => $this->post,
				'taxonomy' => $this->breadcrumbs->postTaxonomy( $this->post->post_type )
			] );
		}
	}
}
