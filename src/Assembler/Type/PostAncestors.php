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
use X3P0\Breadcrumbs\Assembler\AssemblerContext;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;
use X3P0\Breadcrumbs\Support\PostTypes;

/**
 * Walks a post's parent chain to its topmost ancestor, then builds the trail
 * above that ancestor via `PostHierarchy` (and optional `PostTerms`) before
 * adding a crumb for each parent, ordered from the topmost ancestor down to the
 * post's immediate parent. The post itself is not added here. The walk stops if
 * it reaches the front page or a parent whose post type is no longer registered.
 * A parent whose path matches a registered post type archive slug (e.g. a
 * WooCommerce-style shop page that is also a literal `post_parent` of the
 * current post) is forwarded to the `PostType` assembler instead of being
 * added as its own crumb — the archive is registered ahead of the generic
 * page rewrite rules, so it's the source of truth for that URL.
 */
final class PostAncestors extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		AssemblerContext $context,
		private readonly PostTypes $postTypes,
		#[NoAutowire] private readonly WP_Post $post
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		$post        = $this->post;
		$postId      = $post->post_parent;
		$parents     = [];
		$showOnFront = get_option('show_on_front');
		$pageOnFront = absint(get_option('page_on_front'));

		while ($postId) {
			// If we hit a post that's set as the front page, bail.
			if ('posts' !== $showOnFront && $postId === $pageOnFront) {
				break;
			}

			// Get the parent post. Bail if it no longer exists, e.g.
			// the parent was deleted but the child's `post_parent`
			// still points at its ID.
			if (! $parent = get_post($postId)) {
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
			$postId = $post->post_parent;
		}

		// Get the post hierarchy based off the final parent post.
		$this->context->assemble(AssemblerType::PostHierarchy, [
			'post' => $post
		]);

		// Display terms for the post type's configured taxonomy, if any.
		$this->context->assemble(AssemblerType::PostTerms, [
			'post' => $post
		]);

		// Reverse the parents and add their crumbs, forwarding any parent
		// whose path matches a registered post type archive slug to the
		// `PostType` assembler instead. Also bails out of the loop.
		foreach (array_reverse($parents) as $parent) {
			if ($this->forwardsToPostType($parent)) {
				return;
			}

			$this->context->addCrumb(CrumbType::Post, [
				'post' => $parent
			]);
		}
	}

	/**
	 * Checks if the given post's path matches a registered post type
	 * archive slug and, if so, forwards the call to the `PostType`
	 * assembler in its place.
	 */
	private function forwardsToPostType(WP_Post $post): bool
	{
		if (! $uri = (string) get_page_uri($post)) {
			return false;
		}

		if (! $types = $this->postTypes->withArchiveSlug($uri)) {
			return false;
		}

		$this->context->assemble(AssemblerType::PostType, [
			'postType' => $types[0]
		]);

		return true;
	}
}
