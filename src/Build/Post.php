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
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Post extends Build
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
		// If the post has a parent, follow the parent trail.
		if (0 < $this->post->post_parent) {
			$this->breadcrumbs->build('post-ancestors', [
				'post' => $this->post
			]);

		// If the post doesn't have a parent, get its hierarchy based off the post type.
		} else {
			$this->breadcrumbs->build('post-hierarchy', [
				'post' => $this->post
			]);
		}

		// Display terms for specific post type taxonomy if requested.
		if ($this->breadcrumbs->postTaxonomy($this->post->post_type)) {
			$this->breadcrumbs->build('post-terms', [
				'post'     => $this->post,
				'taxonomy' => get_taxonomy(
					$this->breadcrumbs->postTaxonomy($this->post->post_type)
				)
			]);
		}

		// Build the post crumb.
		$this->breadcrumbs->crumb('post', [ 'post' => $this->post ]);
	}
}
