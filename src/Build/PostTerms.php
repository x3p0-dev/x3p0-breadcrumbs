<?php
/**
 * Post terms build class.
 *
 * Builds breadcrumbs based on the given taxonomy for the post.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Build;

class PostTerms extends Base
{
	/**
	 * Post object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    \WP_Post
	 */
	protected $post;

	/**
	 * Taxonomy slug.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $taxonomy;

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		// Get the post type.
		$post_type = get_post_type( $this->post->ID );

		// Get the post categories.
		$terms = get_the_terms( $this->post->ID, $this->taxonomy );

		// Check that categories were returned.
		if ( $terms && ! is_wp_error( $terms ) ) {

			// Sort the terms by ID and get the first category.
			$terms = wp_list_sort( $terms, 'term_id' );

			$term = get_term( $terms[0], $this->taxonomy );

			// If the category has a parent, add the hierarchy to the trail.
			if ( 0 < $term->parent ) {

				$this->breadcrumbs->build( 'TermAncestors', [
					'term' => $term
				] );
			}

			// Add term crumb.
			$this->breadcrumbs->crumb( 'Term', [ 'term' => $term ] );
		}
	}
}
