<?php

/**
 * Post ancestors assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Builder;

/**
 * Assembles breadcrumbs based on whether a post has a parent post. It loops
 * through each post until a parent post is no longer found.
 */
class PostAncestors extends Assembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected WP_Post $post
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
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
			$parent = get_post($post_id);

			// Don't add the post unless the post type still exists.
			// This can happen when a post has a parent with a post
			// type that is no longer registered. For example, an
			// attachment that was uploaded to a post with a type,
			// such as `product`.
			if (! post_type_exists($parent->post_type)) {
				break;
			}

			// Add the post item to the array of parents.
			$post      = $parent;
			$parents[] = $post;

			// If there's no longer a post parent, break out of the loop.
			if (0 >= $post->post_parent) {
				break;
			}

			// Change the post ID to the parent post to continue looping.
			$post_id = $post->post_parent;
		}

		// Get the post hierarchy based off the final parent post.
		$this->builder->assemble('post-hierarchy', [ 'post' => $post ]);

		// Display terms for specific post type taxonomy if requested.
		if ($this->builder->getPostTaxonomy($post->post_type)) {
			$this->builder->assemble('post-terms', [
				'post'     => $post,
				'taxonomy' => get_taxonomy(
					$this->builder->getPostTaxonomy($post->post_type)
				)
			]);
		}

		if ($parents) {
			array_map(function ($parent) {
				$this->builder->addCrumb('post', [ 'post' => $parent ]);
			}, array_reverse($parents));
		}
	}
}
