<?php
/**
 * Post hierarchy build class.
 *
 * Builds breadcrumbs primarily based on the post type rewrite settings of the
 * given post.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use X3P0\Breadcrumbs\Crumb\PostType;

class PostHierarchy extends Base
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
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		// Get the post type.
		$type = get_post_type_object( get_post_type( $this->post->ID ) );

		// If this is the 'post' post type, get the rewrite front items,
		// map the rewrite tags, and bail early.
		if ( 'post' === $type->name ) {

			// Add $wp_rewrite->front to the trail.
			$this->breadcrumbs->build( 'RewriteFront' );

			// Map the rewrite tags.
			$this->breadcrumbs->build( 'MapRewriteTags', [
				'post' => $this->post,
				'path' => get_option( 'permalink_structure' )
			] );

			return;
		}

		$rewrite        = $type->rewrite;
		$done_post_type = false;

		// If the post type has rewrite rules.
		if ( $rewrite ) {

			// Build the rewrite front crumbs.
			if ( $rewrite['with_front'] ) {

				$this->breadcrumbs->build( 'RewriteFront' );
			}

			// If there's a path, check for parents.
			if ( $rewrite['slug'] ) {
				$this->breadcrumbs->build( 'Path', [ 'path' => $rewrite['slug'] ] );

				// Check if we've added a post type crumb.
				foreach ( $this->breadcrumbs->all() as $crumb ) {
					if ( $crumb instanceof PostType ) {
						$done_post_type = true;
						break;
					}
				}
			}
		}

		// Fall back to the post type crumb if not getting from path.
		if ( ! $done_post_type && $type->has_archive ) {
			$this->breadcrumbs->build( 'PostType', [ 'post_type' => $type ] );
		}

		// Map the rewrite tags if there's a `%` in the slug.
		if ( $rewrite && false !== strpos( $rewrite['slug'], '%' ) ) {

			$this->breadcrumbs->build( 'MapRewriteTags', [
				'post' => $this->post,
				'path' => $rewrite['slug']
			] );
		}
	}
}
