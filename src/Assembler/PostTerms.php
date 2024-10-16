<?php

/**
 * Post terms assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use WP_Post;
use WP_Taxonomy;
use X3P0\Breadcrumbs\Contracts\Builder;

/**
 * Assembles breadcrumbs based on the given taxonomy for the post.
 */
class PostTerms extends Assembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected WP_Post $post,
		protected WP_Taxonomy $taxonomy
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
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
				$this->builder->assemble('term-ancestors', [
					'term' => $term
				]);
			}

			// Add term crumb.
			$this->builder->addCrumb('term', [ 'term' => $term ]);
		}
	}
}
