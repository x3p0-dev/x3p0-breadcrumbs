<?php

/**
 * Term ancestors assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Term;
use X3P0\Breadcrumbs\Assembler\AbstractAssembler;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;

/**
 * Assembles breadcrumbs based on whether a term has a parent post. It loops
 * through each term until a parent term is no longer found.
 */
final class TermAncestors extends AbstractAssembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected WP_Term $term
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		$term_id  = $this->term->parent;
		$taxonomy = $this->term->taxonomy;
		$parents  = [];

		while ($term_id) {
			// Get the parent term.
			$term = get_term($term_id, $taxonomy);

			// Add the term link to the array of parent terms.
			$parents[] = $term;

			// Set the parent term's parent as the parent ID.
			$term_id = $term->parent;
		}

		// If we have parent terms, reverse the array to put them in the
		// proper order for the trail.
		if ($parents) {
			array_map(
				fn($parent) => $this->context->addCrumb(
					CrumbRegistrar::TERM,
					[ 'term' => $parent ]
				),
				array_reverse($parents)
			);
		}
	}
}
