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
use X3P0\Breadcrumbs\Assembler\AbstractAssembler;
use X3P0\Breadcrumbs\BreadcrumbsContext;

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
		protected BreadcrumbsContext $context,
		protected WP_Post $post
	) {
		parent::__construct(...func_get_args());
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
		if (! $type = get_post_type_object(get_post_type($this->post->ID))) {
			return;
		};

		// If this is the 'post' post type, get the rewrite front items,
		// map the rewrite tags, and bail early.
		if ('post' === $type->name) {
			// Add $wp_rewrite->front to the trail.
			$this->context->assemble('rewrite-front');

			// Map the rewrite tags.
			$this->context->assemble('post-rewrite-tags', [
				'post' => $this->post,
				'path' => get_option('permalink_structure')
			]);

			return;
		}

		$rewrite        = $type->rewrite;
		$done_post_type = false;

		// If the post type has rewrite rules.
		if ($rewrite) {
			// Assembler the rewrite front crumbs.
			if ($rewrite['with_front']) {
				$this->context->assemble('rewrite-front');
			}

			// If there's a path, check for parents.
			if ($rewrite['slug']) {
				$this->context->assemble('path', [
					'path' => $rewrite['slug']
				]);

				// Check if we've added a post type crumb.
				if ($this->context->crumbs()->has('post-type')) {
					$done_post_type = true;
				}
			}
		}

		// Fall back to the post type crumb if not getting from path.
		if (! $done_post_type && $type->has_archive) {
			$this->context->assemble('post-type', [ 'postType' => $type ]);
		}

		// Map the rewrite tags if there's a `%` in the slug.
		if ($rewrite && str_contains($rewrite['slug'], '%')) {
			$this->context->assemble('post-rewrite-tags', [
				'post' => $this->post,
				'path' => $rewrite['slug']
			]);
		}
	}
}
