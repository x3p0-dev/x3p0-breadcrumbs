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

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\QueryFactory;
use X3P0\Breadcrumbs\Query\QueryType;

/**
 * A facade over the query, assembler, and crumb factories, bundled with the
 * shared crumb collection and config, and passed through the build pipeline as
 * a context object. Each participant (query, assembler, or crumb) receives this
 * so it can dispatch the next step and read or append shared state — without
 * depending on the factories or on one another directly.
 *
 * The `query()`, `assemble()`, and `addCrumb()` methods are the facade: they
 * hide the repeated dance of normalizing a type to its string key, building the
 * object through its factory (injecting this context), and invoking it. The
 * crumb collection is mutable and accumulated into as the pipeline runs; the
 * config is read-only.
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
	 * to the trail. Accepts a `QueryType` for built-in queries or a string key
	 * for custom ones registered by third parties.
	 */
	public function query(QueryType|string $type, array $params = []): void
	{
		$query = $this->queryFactory->make($type, [
			'context' => $this,
			...$params
		]);

		$query?->query();
	}

	/**
	 * Dispatches an assembler by type, injecting this context so the assembler
	 * can add to the trail. Accepts an `AssemblerType` for built-in assemblers
	 * or a string key for custom ones registered by third parties.
	 */
	public function assemble(AssemblerType|string $type, array $params = []): void
	{
		$assembler = $this->assemblerFactory->make($type, [
			'context' => $this,
			...$params
		]);

		$assembler?->assemble();
	}

	/**
	 * Builds a crumb by type and returns it without adding it to the
	 * collection, injecting this context. Accepts a `CrumbType` for built-in
	 * crumbs or a string key for custom ones registered by third parties.
	 * Returns null when the type is not registered. Useful for extensions
	 * that need a crumb instance to hand to the collection's insert or
	 * replace methods on the `CrumbsBuilt` event.
	 */
	public function makeCrumb(CrumbType|string $type, array $params = []): ?Crumb
	{
		return $this->crumbFactory->make($type, [
			'context' => $this,
			...$params
		]);
	}

	/**
	 * Builds a crumb by type and appends it to the shared collection, keyed
	 * by its type. Accepts a `CrumbType` for built-in crumbs or a string
	 * key for custom ones registered by third parties.
	 */
	public function addCrumb(CrumbType|string $type, array $params = []): void
	{
		$key = $type instanceof CrumbType ? $type->value : $type;

		if ($crumb = $this->makeCrumb($type, $params)) {
			$this->crumbs->push($key, $crumb);
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
