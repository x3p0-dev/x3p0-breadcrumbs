<?php

/**
 * Post hierarchy assembler.
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
 * Assembles breadcrumbs primarily based on the post type rewrite settings of
 * the given post.
 */
final class PostHierarchy extends AbstractAssembler
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
		// Get the post type. If there isn't one, bail early. This can
		// happen when a post has a parent with a post type that is no
		// longer registered. For example, an attachment that was
		// uploaded to a post with a type, such as `product`.
		if (! $postType = get_post_type_object(get_post_type($this->post->ID))) {
			return;
		}

		// If this is the 'post' post type, get the rewrite front items,
		// map the rewrite tags, and bail early.
		if ('post' === $postType->name) {
			// Add $wp_rewrite->front to the trail.
			$this->context->assemble(AssemblerRegistrar::REWRITE_FRONT);

			// Assemble the post type crumb. In this case, it will
			// end up being a page if 'posts' are set to a custom
			// page. Otherwise, posts don't have an archive.
			//
			// Note: there are scenarios where both rewrite front
			// and the posts page appears, and this might be weird.
			// But I've left both here for consistency with CPTs
			// (see below) until a better resolution is found.
			$this->context->assemble(AssemblerRegistrar::POST_TYPE, [
				'postType' => $postType
			]);

			// Map the rewrite tags.
			$this->context->assemble(AssemblerRegistrar::POST_REWRITE_TAGS, [
				'post' => $this->post,
				'path' => get_option('permalink_structure')
			]);

			return;
		}

		$rewrite = $postType->rewrite;

		// If the post type has rewrite rules.
		if ($rewrite) {
			// Assemble the rewrite front crumbs.
			if ($rewrite['with_front']) {
				$this->context->assemble(AssemblerRegistrar::REWRITE_FRONT);
			}

			// If there's a path, check for parents.
			if ($rewrite['slug']) {
				$this->context->assemble(AssemblerRegistrar::PATH, [
					'path' => $rewrite['slug']
				]);
			}
		}

		// Assemble the post type if it supports an archive.
		if ($postType->has_archive) {
			$this->context->assemble(AssemblerRegistrar::POST_TYPE, [
				'postType' => $postType
			]);
		}

		// Map the rewrite tags if there's a `%` in the slug.
		if ($rewrite && str_contains($rewrite['slug'], '%')) {
			$this->context->assemble(AssemblerRegistrar::POST_REWRITE_TAGS, [
				'post' => $this->post,
				'path' => $rewrite['slug']
			]);
		}
	}
}
