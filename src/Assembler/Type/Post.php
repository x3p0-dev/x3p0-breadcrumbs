<?php

/**
 * Post assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post;
use X3P0\Breadcrumbs\Assembler\AbstractAssembler;
use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * This is a wrapper to determine a more specific post-related Assembler class to
 * call based on the given post.
 */
final class Post extends AbstractAssembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected WP_Post $post
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
	{
		// If the post has a parent, follow the parent trail.
		if (0 < $this->post->post_parent) {
			$this->context->assemble('post-ancestors', [
				'post' => $this->post
			]);

		// If the post doesn't have a parent, get its hierarchy based off the post type.
		} else {
			$this->context->assemble('post-hierarchy', [
				'post' => $this->post
			]);
		}

		// Display terms for specific post type taxonomy if requested.
		if ($this->context->config()->getPostTaxonomy($this->post->post_type)) {
			$this->context->assemble('post-terms', [
				'post'     => $this->post,
				'taxonomy' => get_taxonomy(
					$this->context->config()->getPostTaxonomy($this->post->post_type)
				)
			]);
		}

		// Assembler the post crumb.
		$this->context->addCrumb('post', [ 'post' => $this->post ]);
	}
}
