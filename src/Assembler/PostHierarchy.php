<?php

/**
 * Post hierarchy assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Crumb\PostType;

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
		protected Breadcrumbs $breadcrumbs,
		protected WP_Post $post
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		// Get the post type.
		$type = get_post_type_object(get_post_type($this->post->ID));

		// If this is the 'post' post type, get the rewrite front items,
		// map the rewrite tags, and bail early.
		if ('post' === $type->name) {
			// Add $wp_rewrite->front to the trail.
			$this->breadcrumbs->assemble('rewrite-front');

			// Map the rewrite tags.
			$this->breadcrumbs->assemble('post-rewrite-tags', [
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
				$this->breadcrumbs->assemble('rewrite-front');
			}

			// If there's a path, check for parents.
			if ($rewrite['slug']) {
				$this->breadcrumbs->assemble('path', [
					'path' => $rewrite['slug']
				]);

				// Check if we've added a post type crumb.
				foreach ($this->breadcrumbs->getCrumbs() as $crumb) {
					if ($crumb instanceof PostType) {
						$done_post_type = true;
						break;
					}
				}
			}
		}

		// Fall back to the post type crumb if not getting from path.
		if (! $done_post_type && $type->has_archive) {
			$this->breadcrumbs->assemble('post-type', [ 'post_type' => $type ]);
		}

		// Map the rewrite tags if there's a `%` in the slug.
		if ($rewrite && false !== strpos($rewrite['slug'], '%')) {
			$this->breadcrumbs->assemble('post-rewrite-tags', [
				'post' => $this->post,
				'path' => $rewrite['slug']
			]);
		}
	}
}
