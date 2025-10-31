<?php

/**
 * Post terms assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post;
use WP_Taxonomy;
use X3P0\Breadcrumbs\Assembler\AbstractAssembler;
use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * Assembles breadcrumbs based on the given taxonomy for the post.
 */
final class PostTerms extends AbstractAssembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected WP_Post $post,
		protected WP_Taxonomy $taxonomy
	) {
		parent::__construct(...func_get_args());
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
			// Get the first term object.
			$term = $terms[0];

			// If the term has a parent, add its ancestor crumbs to
			// the breadcrumb trail.
			if (0 < $term->parent) {
				$this->context->assemble('term-ancestors', [
					'term' => $term
				]);
			}

			// Add term crumb.
			$this->context->addCrumb('term', [ 'term' => $term ]);
		}
	}
}
