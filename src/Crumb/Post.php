<?php

/**
 * Post crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Post extends Crumb
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
	public function label(): string
	{
		$post_id = $this->post->ID;

		if (is_single($post_id) || is_page($post_id) || is_attachment($post_id)) {
			return single_post_title('', false);
		}

		return get_the_title($this->post->ID);
	}

	/**
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return get_permalink($this->post->ID);
	}
}
