<?php

/**
 * Post ancestors build class.
 *
 * Builds breadcrumbs based on whether a post has a parent post. It loops
 * through each post until a parent post is no longer found.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class PostAncestors extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Post $post
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$post    = $this->post;
		$post_id = $post->post_parent;
		$parents = [];

		while ($post_id) {
			$show_on_front = get_option('show_on_front');
			$page_on_front = get_option('page_on_front');

			// If we hit a post that's set as the front page, bail.
			if ('posts' !== $show_on_front && $post_id === $page_on_front) {
				break;
			}

			// Get the parent post.
			$post = get_post($post_id);

			// Add the formatted post item to the array of parents.
			$parents[] = $post;

			// If there's no longer a post parent, break out of the loop.
			if (0 >= $post->post_parent) {
				break;
			}

			// Change the post ID to the parent post to continue looping.
			$post_id = $post->post_parent;
		}

		// Get the post hierarchy based off the final parent post.
		$this->breadcrumbs->build('post-hierarchy', [ 'post' => $post ]);

		// Display terms for specific post type taxonomy if requested.
		if ($this->breadcrumbs->postTaxonomy($post->post_type)) {
			$this->breadcrumbs->build('post-terms', [
				'post'     => $post,
				'taxonomy' => get_taxonomy(
					$this->breadcrumbs->postTaxonomy($post->post_type)
				)
			]);
		}

		if ($parents) {
			array_map(function ($parent) {
				$this->breadcrumbs->crumb('post', [ 'post' => $parent ]);
			}, array_reverse($parents));
		}
	}
}
