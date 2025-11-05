<?php

/**
 * Term assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Term;
use X3P0\Breadcrumbs\Assembler\{AbstractAssembler, AssemblerRegistrar};
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;

/**
 * Assembles breadcrumbs based on the given term object.
 */
final class Term extends AbstractAssembler
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
		$taxonomy       = get_taxonomy($this->term->taxonomy);
		$done_post_type = false;

		// Will either be `false` or an array.
		$rewrite = $taxonomy->rewrite;

		// Assemble rewrite front crumbs if taxonomy uses it.
		if ($rewrite && $rewrite['with_front']) {
			$this->context->assemble(AssemblerRegistrar::REWRITE_FRONT);
		}

		// Assemble crumbs based on the rewrite slug.
		if ($rewrite && $rewrite['slug']) {
			$path = trim($rewrite['slug'], '/');

			// Assemble path crumbs.
			$this->context->assemble(AssemblerRegistrar::PATH, [
				'path' => $path
			]);

			// Check if we've added a post type crumb.
			if ($this->context->crumbs()->has(CrumbRegistrar::POST_TYPE)) {
				$done_post_type = true;
			}
		}

		// If the taxonomy has a single post type.
		if (! $done_post_type && 1 === count($taxonomy->object_type)) {
			$this->context->assemble(AssemblerRegistrar::POST_TYPE, [
				'postType' => get_post_type_object(
					$taxonomy->object_type[0]
				)
			]);
		}

		// If the taxonomy is hierarchical, list the parent terms.
		if (is_taxonomy_hierarchical($taxonomy->name) && $this->term->parent) {
			$this->context->assemble(AssemblerRegistrar::TERM_ANCESTORS, [
				'term' => $this->term
			]);
		}

		// Add the term crumb.
		$this->context->addCrumb(CrumbRegistrar::TERM, [
			'term' => $this->term
		]);
	}
}
