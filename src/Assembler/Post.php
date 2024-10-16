<?php

/**
 * Post assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Builder;

/**
 * This is a wrapper to determine a more specific post-related Assembler class to
 * call based on the given post.
 */
class Post extends Assembler
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
		// If the post has a parent, follow the parent trail.
		if (0 < $this->post->post_parent) {
			$this->builder->assemble('post-ancestors', [
				'post' => $this->post
			]);

		// If the post doesn't have a parent, get its hierarchy based off the post type.
		} else {
			$this->builder->assemble('post-hierarchy', [
				'post' => $this->post
			]);
		}

		// Display terms for specific post type taxonomy if requested.
		if ($this->builder->getPostTaxonomy($this->post->post_type)) {
			$this->builder->assemble('post-terms', [
				'post'     => $this->post,
				'taxonomy' => get_taxonomy(
					$this->builder->getPostTaxonomy($this->post->post_type)
				)
			]);
		}

		// Assembler the post crumb.
		$this->builder->addCrumb('post', [ 'post' => $this->post ]);
	}
}
