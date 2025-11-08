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
use X3P0\Breadcrumbs\Assembler\{AbstractAssembler, AssemblerRegistrar};
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;

/**
 * This is a wrapper to determine a more specific post-related Assembler class to
 * call based on the given post.
 */
final class Post extends AbstractAssembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		protected WP_Post $post
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		// If the post has a parent, follow the parent trail.
		if (0 < $this->post->post_parent) {
			$this->context->assemble(AssemblerRegistrar::POST_ANCESTORS, [
				'post' => $this->post
			]);

		// If the post doesn't have a parent, get its hierarchy based off the post type.
		} else {
			$this->context->assemble(AssemblerRegistrar::POST_HIERARCHY, [
				'post' => $this->post
			]);
		}

		// Display terms for specific post type taxonomy if requested.
		if ($taxonomy = $this->context->config()->getPostTaxonomy($this->post->post_type)) {
			$this->context->assemble(AssemblerRegistrar::POST_TERMS, [
				'post'     => $this->post,
				'taxonomy' => get_taxonomy($taxonomy)
			]);
		}

		// Assembler the post crumb.
		$this->context->addCrumb(CrumbRegistrar::POST, [
			'post' => $this->post
		]);
	}
}
