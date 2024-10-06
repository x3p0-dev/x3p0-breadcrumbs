<?php

/**
 * Post type crumb class.
 *
 * Creates the post type archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use WP_Post_Type;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class PostType extends Base
{
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Post_Type $post_type
	) {}

	public function label(): string
	{
		if (is_post_type_archive($this->post_type->name)) {
			return post_type_archive_title('', false);
		}

		$labels = $this->post_type->labels;

		return apply_filters(
			'post_type_archive_title', // Core WP filter hook.
			! empty($labels->archive_title) ? $labels->archive_title : $labels->name,
			$this->post_type->name
		);
	}

	public function url(): string
	{
		return get_post_type_archive_link($this->post_type->name);
	}
}
