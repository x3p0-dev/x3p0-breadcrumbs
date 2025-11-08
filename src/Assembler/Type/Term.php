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
		BreadcrumbsContext $context,
		protected WP_Term $term
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		$taxonomy = get_taxonomy($this->term->taxonomy);
		$rewrite  = $taxonomy->rewrite; // false|array

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
		}

		// If there's not already a post type crumb and the taxonomy has
		// a single post type, add its crumb since the taxonomy should
		// be considered a part of the larger content type.
		if (
			! $this->context->crumbs()->has(CrumbRegistrar::POST_TYPE)
			&& 1 === count($taxonomy->object_type)
		) {
			$this->context->assemble(AssemblerRegistrar::POST_TYPE, [
				'postType' => get_post_type_object(
					$taxonomy->object_type[0]
				)
			]);
		}

		// If the term has a parent, get its ancestors.
		if (0 < $this->term->parent) {
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
