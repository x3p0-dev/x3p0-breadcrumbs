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
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Crumb\Type\PostType;

/**
 * Assembles breadcrumbs based on the given term object.
 */
class Term extends Assembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected WP_Term $term
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
	{
		$taxonomy       = get_taxonomy($this->term->taxonomy);
		$done_post_type = false;

		// Will either be `false` or an array.
		$rewrite = $taxonomy->rewrite;

		// Assembler rewrite front crumbs if taxonomy uses it.
		if ($rewrite && $rewrite['with_front']) {
			$this->builder->assemble('rewrite-front');
		}

		// Assembler crumbs based on the rewrite slug.
		if ($rewrite && $rewrite['slug']) {
			$path = trim($rewrite['slug'], '/');

			// Assembler path crumbs.
			$this->builder->assemble('path', [ 'path' => $path ]);

			// Check if we've added a post type crumb.
			$crumbs = $this->builder->getCrumbs();
			$crumbs->rewind();

			while ($crumbs->valid()) {
				if ($crumbs->current()->isType('post-type')) {
					$done_post_type = true;
					break;
				}
				$crumbs->next();
			}
		}

		// If the taxonomy has a single post type.
		if (! $done_post_type && 1 === count($taxonomy->object_type)) {
			$this->builder->assemble('post-type', [
				'type' => get_post_type_object(
					$taxonomy->object_type[0]
				)
			]);
		}

		// If the taxonomy is hierarchical, list the parent terms.
		if (is_taxonomy_hierarchical($taxonomy->name) && $this->term->parent) {
			$this->builder->assemble('term-ancestors', [ 'term' => $this->term ]);
		}

		// Assembler the term crumb.
		$this->builder->addCrumb('term', [ 'term' => $this->term ]);
	}
}
