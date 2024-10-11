<?php

/**
 * Post terms builder.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Builder;

use WP_Post;
use WP_Taxonomy;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

/**
 * Builds breadcrumbs based on the given taxonomy for the post.
 */
class PostTerms extends Builder
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Post $post,
		protected WP_Taxonomy $taxonomy
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		// Get the post terms for the given taxonomy.
		$terms = get_the_terms($this->post->ID, $this->taxonomy->name);

		// Check that terms were returned.
		if ($terms && ! is_wp_error($terms)) {
			// Sort the terms by ID and get the first term.
			$terms = wp_list_sort($terms, 'term_id');
			$term  = get_term($terms[0], $this->taxonomy->name);

			// If the term has a parent, add its ancestor crumbs to
			// the breadcrumb trail.
			if (0 < $term->parent) {
				$this->breadcrumbs->build('term-ancestors', [
					'term' => $term
				]);
			}

			// Add term crumb.
			$this->breadcrumbs->crumb('term', [ 'term' => $term ]);
		}
	}
}
