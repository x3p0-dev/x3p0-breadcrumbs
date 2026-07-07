<?php

/**
 * Post ancestors assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post;
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Walks a post's parent chain to its topmost ancestor, then builds the trail
 * above that ancestor via `PostHierarchy` (and optional `PostTerms`) before
 * adding a crumb for each parent, ordered from the topmost ancestor down to the
 * post's immediate parent. The post itself is not added here. The walk stops if
 * it reaches the front page or a parent whose post type is no longer registered.
 */
final class PostAncestors extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private readonly WP_Post $post
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		$post          = $this->post;
		$post_id       = $post->post_parent;
		$parents       = [];
		$show_on_front = get_option('show_on_front');
		$page_on_front = get_option('page_on_front');

		while ($post_id) {
			// If we hit a post that's set as the front page, bail.
			if ('posts' !== $show_on_front && $post_id === $page_on_front) {
				break;
			}

			// Get the parent post. Bail if it no longer exists, e.g.
			// the parent was deleted but the child's `post_parent`
			// still points at its ID.
			if (! $parent = get_post($post_id)) {
				break;
			}

			// Don't add the post unless the post type still exists.
			// This can happen when a post has a parent with a post
			// type that is no longer registered. For example, an
			// attachment that was uploaded to a post with a type,
			// such as `product`.
			if (! post_type_exists($parent->post_type)) {
				break;
			}

			// Add the post item to the array of parents.
			$post      = $parent;
			$parents[] = $post;

			// If there's no longer a post parent, break out of the loop.
			if (0 >= $post->post_parent) {
				break;
			}

			// Change the post ID to the parent post to continue looping.
			$post_id = $post->post_parent;
		}

		// Get the post hierarchy based off the final parent post.
		$this->context->assemble(AssemblerType::PostHierarchy, [
			'post' => $post
		]);

		// Display terms for specific post type taxonomy if requested.
		if ($taxonomy = $this->context->config()->getPostTaxonomy($post->post_type)) {
			$this->context->assemble(AssemblerType::PostTerms, [
				'post'     => $post,
				'taxonomy' => get_taxonomy($taxonomy)
			]);
		}

		// Reverse the parents and add their crumbs.
		foreach (array_reverse($parents) as $parent) {
			$this->context->addCrumb(CrumbType::Post, [
				'post' => $parent
			]);
		}
	}
}
