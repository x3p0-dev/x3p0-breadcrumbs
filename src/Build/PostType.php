<?php

/**
 * Post type build class.
 *
 * Builds breadcrumbs for the give post type.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use WP_Post_Type;
use WP_Rewrite;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class PostType extends Base
{
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Post_Type $post_type = null
	) {}

	/**
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function make(): void
	{
		if (! $type = $this->post_type) {
			return;
		}

		// If this the post type is `post`, add the posts page and bail.
		if ('post' === $type->name) {
			$show_on_front = get_option('show_on_front');
			$post_id       = get_option('page_for_posts');

			// Add post crumb if we have a posts page.
			if ('posts' !== $show_on_front && 0 < $post_id) {
				$post = get_post($post_id);

				// If the posts page is the same as the rewrite
				// front path, we should've already handled that
				// scenario at this point.
				if (trim($GLOBALS['wp_rewrite']->front, '/') !== $post->post_name) {
					$this->breadcrumbs->crumb('post', [
						'post' => $post
					]);
				}
			}

			return;
		}

		// Add post type crumb.
		$this->breadcrumbs->crumb('post-type', [ 'post_type' => $type ]);
	}
}
