<?php

/**
 * Post type archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use WP_Post_Type;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Crumb\PostType;

class PostTypeArchive extends Base
{
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Post_Type $post_type = null,
		protected ?WP_User $user = null
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$type = $this->post_type ?: get_post_type_object(get_query_var('post_type'));

		$done_post_type = false;

		$this->breadcrumbs->build('home');

		if (false !== $type->rewrite) {
			// Build rewrite front crumbs if post type uses it.
			if ($type->rewrite['with_front']) {
				$this->breadcrumbs->build('rewrite-front');
			}

			// If there's a rewrite slug, check for parents.
			if (! empty($type->rewrite['slug'])) {
				$this->breadcrumbs->build('path', [ 'path' => $type->rewrite['slug'] ]);

				// Check if we've added a post type crumb.
				foreach ($this->breadcrumbs->all() as $crumb) {
					if ($crumb instanceof PostType) {
						$done_post_type = true;
						break;
					}
				}
			}
		}

		// Add post type crumb.
		if (! $done_post_type) {
			$this->breadcrumbs->crumb('post-type', [ 'post_type' => $type ]);
		}

		// If viewing a post type search, add the search crumb. This
		// handles URLs like `/?s={search}&post_type={type}`.
		if (is_search()) {
			$this->breadcrumbs->crumb('search');
		}

		// If viewing a post type archive by author, add author crumb.
		// This handles URLs like `/{type}?=author={author}`.
		if (is_author()) {
			$user = $this->user ?: new WP_User(get_query_var('author'));

			// Add author crumb.
			$this->breadcrumbs->crumb('author', [ 'user' => $user ]);
		}

		$this->breadcrumbs->build('paged');
	}
}
