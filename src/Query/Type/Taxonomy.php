<?php

/**
 * Taxonomy query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Exception;
use WP_Term;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a taxonomy term archive (category, tag, or custom
 * taxonomy): the home step, the term step (which expands the term's ancestry),
 * and the paged step.
 */
final class Taxonomy extends Query
{
	/**
	 * @inheritDoc
	 *
	 * @param WP_Term $term Optional term to build for; falls back to the
	 *                      queried object when omitted.
	 */
	public function __construct(
		BreadcrumbsContext $context,
		#[NoAutowire] private readonly ?WP_Term $term = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 * @throws WP_Exception
	 */
	public function query(): void
	{
		$term = $this->term ?: $this->queriedObject(WP_Term::class);

		$this->context->assemble(AssemblerType::Home);

		// Skip the term step when the queried object is not a term, so
		// a query left in an unexpected state degrades to a safe trail
		// instead of passing a wrong type into the term assembler.
		if ($term instanceof WP_Term) {
			$this->context->assemble(AssemblerType::Term, [ 'term' => $term ]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
