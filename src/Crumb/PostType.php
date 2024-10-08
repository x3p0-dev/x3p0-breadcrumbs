<?php

/**
 * Post type crumb class.
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
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Post_Type $type
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function label(): string
	{
		if (is_post_type_archive($this->type->name)) {
			return post_type_archive_title('', false);
		}

		return apply_filters(
			'post_type_archive_title', // Core WP filter hook.
			$this->type->labels->name,
			$this->type->name
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return get_post_type_archive_link($this->type->name);
	}
}
