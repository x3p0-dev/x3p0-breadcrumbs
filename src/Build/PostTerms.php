<?php

/**
 * Post terms build class.
 *
 * Builds breadcrumbs based on the given taxonomy for the post.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use WP_Post;
use WP_Taxonomy;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class PostTerms extends Base
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
		// Get the post categories.
		$terms = get_the_terms($this->post->ID, $this->taxonomy->name);

		// Check that categories were returned.
		if ($terms && ! is_wp_error($terms)) {
			// Sort the terms by ID and get the first category.
			$terms = wp_list_sort($terms, 'term_id');

			$term = get_term($terms[0], $this->taxonomy->name);

			// If the category has a parent, add the hierarchy to the trail.
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
