<?php

/**
 * Term ancestors assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Term;
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Walks a term's parent chain to the top of its hierarchy and adds a crumb for
 * each ancestor, ordered from the topmost ancestor down to the term's immediate
 * parent. The term itself is not added here.
 */
final class TermAncestors extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private WP_Term $term
	) {
		parent::__construct(context: $context);
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

		// Reverse the parents and add their crumbs.
		foreach (array_reverse($parents) as $parent) {
			$this->context->addCrumb(CrumbType::Term, [
				'term' => $parent
			]);
		}
	}
}
