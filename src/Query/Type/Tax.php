<?php

/**
 * Taxonomy query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Term;
use X3P0\Breadcrumbs\Assembler\AssemblerRegistrar;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Query\AbstractQuery;

final class Tax extends AbstractQuery
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		protected ?WP_Term $term = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$term = $this->term ?: get_queried_object();

		$this->context->assemble(AssemblerRegistrar::HOME);
		$this->context->assemble(AssemblerRegistrar::TERM, [ 'term' => $term ]);
		$this->context->assemble(AssemblerRegistrar::PAGED);
	}
}
