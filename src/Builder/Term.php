<?php

/**
 * Term builder.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Builder;

use WP_Term;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Crumb\PostType;

/**
 * Builds breadcrumbs based on the given term object.
 */
class Term extends Builder
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Term $term
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$taxonomy       = get_taxonomy($this->term->taxonomy);
		$done_post_type = false;

		// Will either be `false` or an array.
		$rewrite = $taxonomy->rewrite;

		// Builder rewrite front crumbs if taxonomy uses it.
		if ($rewrite && $rewrite['with_front']) {
			$this->breadcrumbs->build('rewrite-front');
		}

		// Builder crumbs based on the rewrite slug.
		if ($rewrite && $rewrite['slug']) {
			$path = trim($rewrite['slug'], '/');

			// Builder path crumbs.
			$this->breadcrumbs->build('path', [ 'path' => $path ]);

			// Check if we've added a post type crumb.
			foreach ($this->breadcrumbs->getCrumbs() as $crumb) {
				if ($crumb instanceof PostType) {
					$done_post_type = true;
					break;
				}
			}
		}

		// If the taxonomy has a single post type.
		if (! $done_post_type && 1 === count($taxonomy->object_type)) {
			$this->breadcrumbs->build('post-type', [
				'post_type' => get_post_type_object(
					$taxonomy->object_type[0]
				)
			]);
		}

		// If the taxonomy is hierarchical, list the parent terms.
		if (is_taxonomy_hierarchical($taxonomy->name) && $this->term->parent) {
			$this->breadcrumbs->build('term-ancestors', [ 'term' => $this->term ]);
		}

		// Builder the term crumb.
		$this->breadcrumbs->crumb('term', [ 'term' => $this->term ]);
	}
}
