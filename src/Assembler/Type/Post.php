<?php

/**
 * Post assembler.
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
use X3P0\Breadcrumbs\Crumb\Type\Post as PostCrumb;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;
use X3P0\Breadcrumbs\Support\PostTypes;

/**
 * Builds the trail leading up to a single post and adds the post's own crumb.
 * It delegates the ancestor/hierarchy portion to `PostAncestors` (when the post
 * has a parent) or `PostHierarchy` (when it does not), optionally inserts a
 * representative term crumb via `PostTerms`, and finally appends the post crumb.
 * Bails if the post is already in the collection, or if its path matches a
 * registered post type archive slug (e.g. a WooCommerce-style shop page
 * resolved by path) — the archive is registered ahead of the generic page
 * rewrite rules, so it's the source of truth for that URL. In the latter
 * case, the call is forwarded to the `PostType` assembler instead.
 */
final class Post extends Assembler
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
		$showOnFront = get_option('show_on_front');
		$pageOnFront = absint(get_option('page_on_front'));

		// A post set as the site's front page is already represented
		// by the home crumb; never add it again.
		if ('posts' !== $showOnFront && $this->post->ID === $pageOnFront) {
			return;
		}

		// Bail early if the post exists in the crumb collection, or if
		// its path matches a registered post type archive slug (e.g. a
		// WooCommerce-style shop page) — the archive is registered ahead
		// of the generic page rewrite rules, so it's the source of truth
		// for that URL. The latter forwards to the `PostType` assembler.
		if ($this->postCrumbExists() || $this->forwardsToPostType()) {
			return;
		}

		// If the post has a parent, follow the parent trail.
		if (0 < $this->post->post_parent) {
			$this->context->assemble(AssemblerType::PostAncestors, [
				'post' => $this->post
			]);

		// If the post doesn't have a parent, get its hierarchy based
		// off the post type.
		} else {
			$this->context->assemble(AssemblerType::PostHierarchy, [
				'post' => $this->post
			]);
		}

		// Display terms for the post type's configured taxonomy, if any.
		$this->context->assemble(AssemblerType::PostTerms, [
			'post' => $this->post
		]);

		// Assemble the post crumb.
		$this->context->addCrumb(CrumbType::Post, [
			'post' => $this->post
		]);
	}

	/**
	 * Checks if the current post already exists in the crumb collection.
	 */
	private function postCrumbExists(): bool
	{
		return $this->context->crumbs->containsInstanceWhere(
			PostCrumb::class,
			fn (PostCrumb $crumb) => $crumb->post->ID === $this->post->ID
		);
	}

	/**
	 * Checks if the current post's path matches a registered post type
	 * archive slug and, if so, forwards the call to the `PostType`
	 * assembler in its place.
	 */
	private function forwardsToPostType(): bool
	{
		if (! $uri = (string) get_page_uri($this->post)) {
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
