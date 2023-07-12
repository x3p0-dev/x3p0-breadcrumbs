<?php
/**
 * Path build class.
 *
 * Builds breadcrumbs based on a given path by attempting to find a post object
 * within that path.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use X3P0\Breadcrumbs\Util\Helpers;

class Path extends Base
{
	/**
	 * Path to search.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $path = '';

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		$path = trim( $this->path, '/' );

		// If there's no path, return.
		if ( ! $path ) {
			return;
		}

		// Get parent post by the path.
		$post = get_page_by_path( $path );

		// If the path is a post, run the parent crumbs and bail early.
		if ( $post ) {
			$this->breadcrumbs->build( 'PostAncestors', [ 'post' => $post ] );

			$this->breadcrumbs->crumb( 'Post', [ 'post' => $post ] );

			return;
		}

		// Split the $path into an array of strings.
		$matches = explode( '/', $path );

		// If matches are found for the path.
		if ( $matches ) {

			// Reverse the array of matches to search for posts in
			// the proper order.
			$matches = array_reverse( $matches );

			// Loop through each of the path matches.
			foreach ( $matches as $slug ) {

				// Get the parent post by the given path.
				$post = get_page_by_path( $slug );

				// If a parent post is found, build the crumbs
				// and break out of the loop.
				if ( ! empty( $post ) && 0 < $post->ID ) {

					$this->breadcrumbs->build( 'PostAncestors', [
						'post' => $post
					] );

					$this->breadcrumbs->crumb( 'Post', [
						'post' => $post
					] );

					break;

				// If the slug matches a post type, let's build
				// that and break out of the loop.
				} elseif ( $types = Helpers::getPostTypesBySlug( $slug ) ) {

					$this->breadcrumbs->build( 'PostType', [
						'post_type' => $types[0]
					] );

					break;
				}
			}
		}
	}
}
