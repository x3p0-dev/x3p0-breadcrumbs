<?php

/**
 * Query context.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Assembler\AssemblerContext;
use X3P0\Breadcrumbs\Assembler\AssemblerDefinition;
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbDefinition;

/**
 * The full capability set: everything {@see AssemblerContext::assemble()}
 * offers, plus query dispatch. Only `Query` ever receives this — `Assembler`
 * always receives the narrower {@see AssemblerContext::assemble()} instead, a
 * genuinely separate object it holds by composition, not a shared instance
 * viewed through a narrower type.
 *
 * `config`, `crumbs`, `assemble()`, `makeCrumb()`, and `addCrumb()` forward to
 * the composed {@see AssemblerContext::assemble()} so `Query` subclasses see
 * the same surface `Assembler` subclasses do, plus `query()`.
 */
final class QueryContext
{
	/**
	 * Read-only config, re-exposed from the composed `AssemblerContext` so
	 * `Query` subclasses can read it directly.
	 */
	public readonly BreadcrumbsConfig $config;

	/**
	 * The mutable crumb collection accumulated so far, re-exposed from the
	 * composed `AssemblerContext` so `Query` subclasses can read it directly.
	 */
	public readonly CrumbCollection $crumbs;

	/**
	 * Stores the factory queries are resolved through and the composed
	 * `AssemblerContext` that `assemble()`/`makeCrumb()`/`addCrumb()`
	 * forward to, then reads `$config` and `$crumbs` from it for direct,
	 * public access.
	 */
	public function __construct(
		private readonly QueryFactory     $queryFactory,
		private readonly AssemblerContext $assemblerContext
	) {
		$this->config = $assemblerContext->config;
		$this->crumbs = $assemblerContext->crumbs;
	}

	/**
	 * Dispatches a query by type, injecting this context so it can delegate
	 * to another query, run an assembler, or add a crumb. Accepts any
	 * `QueryDefinition` enum, `Query` class string, or tagged slug. Does
	 * nothing when the type is unknown.
	 */
	public function query(QueryDefinition|string $type, array $params = []): void
	{
		$this->queryFactory->make($type, [
			'context' => $this,
			...$params
		])?->query();
	}

	/**
	 * Forwards to {@see AssemblerContext::assemble()}.
	 */
	public function assemble(AssemblerDefinition|string $type, array $params = []): void
	{
		$this->assemblerContext->assemble($type, $params);
	}

	/**
	 * Forwards to {@see AssemblerContext::makeCrumb()}.
	 */
	public function makeCrumb(CrumbDefinition|string $type, array $params = []): ?Crumb
	{
		return $this->assemblerContext->makeCrumb($type, $params);
	}

	/**
	 * Forwards to {@see AssemblerContext::addCrumb()}.
	 */
	public function addCrumb(CrumbDefinition|string $type, array $params = []): void
	{
		$this->assemblerContext->addCrumb($type, $params);
	}
}
