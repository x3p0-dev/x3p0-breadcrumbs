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
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Crumb\Type\PostType;

/**
 * Assemblers breadcrumbs primarily based on the post type rewrite settings of the
 * given post.
 */
class PostHierarchy extends Assembler
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
			$this->builder->assemble('rewrite-front');

			// Map the rewrite tags.
			$this->builder->assemble('post-rewrite-tags', [
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
				$this->builder->assemble('rewrite-front');
			}

			// If there's a path, check for parents.
			if ($rewrite['slug']) {
				$this->builder->assemble('path', [
					'path' => $rewrite['slug']
				]);

				// Check if we've added a post type crumb.
				$crumbs = $this->builder->getCrumbs();
				$crumbs->rewind();

				while ($crumbs->valid()) {
					if ($crumbs->current()->isType('post-type')) {
						$done_post_type = true;
						break;
					}
					$crumbs->next();
				}
			}
		}

		// Fall back to the post type crumb if not getting from path.
		if (! $done_post_type && $type->has_archive) {
			$this->builder->assemble('post-type', [ 'type' => $type ]);
		}

		// Map the rewrite tags if there's a `%` in the slug.
		if ($rewrite && str_contains($rewrite['slug'], '%')) {
			$this->builder->assemble('post-rewrite-tags', [
				'post' => $this->post,
				'path' => $rewrite['slug']
			]);
		}
	}
}
