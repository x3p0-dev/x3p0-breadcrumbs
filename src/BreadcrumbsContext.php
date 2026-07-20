<?php

/**
 * Breadcrumbs context.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerDefinition;
use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbDefinition;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Query\QueryDefinition;
use X3P0\Breadcrumbs\Query\QueryFactory;

/**
 * A facade over the query, assembler, and crumb factories, bundled with the
 * shared crumb collection and config, and passed through the build pipeline as a
 * context object. Each participant (query, assembler, or crumb) receives this so
 * it can dispatch the next step and read or append shared state — without
 * depending on the factories or on one another directly.
 *
 * The `query()`, `assemble()`, and `addCrumb()` methods are the facade: each
 * hands the type to its factory (injecting this context) and invokes the built
 * object. The crumb collection is mutable and accumulated into as the pipeline
 * runs; the config is read-only.
 */
final class BreadcrumbsContext
{
	/**
	 * Bundles the shared instances needed to build a single breadcrumb trail.
	 * The crumb collection is the mutable accumulator that the pipeline appends
	 * to; the factories and config are shared, read-only collaborators.
	 */
	public function __construct(
		private readonly CrumbCollection   $crumbs,
		private readonly QueryFactory      $queryFactory,
		private readonly AssemblerFactory  $assemblerFactory,
		private readonly CrumbFactory      $crumbFactory,
		private readonly BreadcrumbsConfig $config
	) {}

	/**
	 * Dispatches a query by type, injecting this context so the query can add
	 * to the trail. Accepts any `QueryType` (the `QueryType` enum or a
	 * third-party implementation), its string key, or a query class name. Does
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
	 * Dispatches an assembler by type, injecting this context so the assembler
	 * can add to the trail. Accepts any `AssemblerType` (the `AssemblerType`
	 * enum or a third-party implementation), its string key, or an assembler
	 * class name. Does nothing when the type is unknown.
	 */
	public function assemble(AssemblerDefinition|string $type, array $params = []): void
	{
		$this->assemblerFactory->make($type, [
			'context' => $this,
			...$params
		])?->assemble();
	}

	/**
	 * Builds a crumb by type and returns it without adding it to the
	 * collection, injecting this context. Accepts any `CrumbType` (the
	 * `CrumbType` enum or a third-party implementation), its string key, or a
	 * crumb class name. Returns null for an unknown type. Useful for extensions
	 * that need a crumb instance to hand to the collection's insert or replace
	 * methods on the `CrumbsBuilt` event.
	 */
	public function makeCrumb(CrumbDefinition|string $type, array $params = []): ?Crumb
	{
		return $this->crumbFactory->make($type, [
			'context' => $this,
			...$params
		]);
	}

	/**
	 * Builds a crumb by type and appends it to the shared collection. Accepts
	 * any `CrumbType` (the `CrumbType` enum or a third-party implementation), its
	 * string key, or a crumb class name.
	 */
	public function addCrumb(CrumbDefinition|string $type, array $params = []): void
	{
		if ($crumb = $this->makeCrumb($type, $params)) {
			$this->crumbs->push($crumb);
		}
	}

	/**
	 * Returns the shared, read-only config for this build.
	 */
	public function config(): BreadcrumbsConfig
	{
		return $this->config;
	}

	/**
	 * Returns the crumb collection accumulated so far.
	 */
	public function crumbs(): CrumbCollection
	{
		return $this->crumbs;
	}
}
