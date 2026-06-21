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
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\QueryFactory;
use X3P0\Breadcrumbs\Query\QueryType;

/**
 * Provides a simple API to pass into query, assembler, and crumb classes so
 * that they can build the crumb collection. Wraps the factories and collection
 * with helper methods to hide complexity.
 */
final class BreadcrumbsContext
{
	/**
	 * Collects the various instances needed for building breadcrumbs as
	 * class properties.
	 */
	public function __construct(
		private readonly CrumbCollection   $crumbs,
		private readonly QueryFactory      $queryFactory,
		private readonly AssemblerFactory  $assemblerFactory,
		private readonly CrumbFactory      $crumbFactory,
		private readonly BreadcrumbsConfig $config
	) {}

	/**
	 * Run a query by type. Accepts a `QueryType` for built-in queries or a
	 * string key for custom ones registered by third parties.
	 */
	public function query(QueryType|string $type, array $params = []): void
	{
		$key = $type instanceof QueryType ? $type->value : $type;

		$query = $this->queryFactory->make($key, [
			'context' => $this,
			...$params
		]);

		$query?->query();
	}

	/**
	 * Run an assembler by type. Accepts an `AssemblerType` for built-in
	 * assemblers or a string key for custom ones registered by third parties.
	 */
	public function assemble(AssemblerType|string $type, array $params = []): void
	{
		$key = $type instanceof AssemblerType ? $type->value : $type;

		$assembler = $this->assemblerFactory->make($key, [
			'context' => $this,
			...$params
		]);

		$assembler?->assemble();
	}

	/**
	 * Add a crumb by type. Accepts a `CrumbType` for built-in crumbs or a
	 * string key for custom ones registered by third parties.
	 */
	public function addCrumb(CrumbType|string $type, array $params = []): void
	{
		$key = $type instanceof CrumbType ? $type->value : $type;

		$crumb = $this->crumbFactory->make($key, [
			'context' => $this,
			...$params
		]);

		if ($crumb) {
			$this->crumbs->set($key, $crumb);
		}
	}

	/**
	 * Access the configuration.
	 */
	public function config(): BreadcrumbsConfig
	{
		return $this->config;
	}

	/**
	 * Access the crumb collection.
	 */
	public function crumbs(): CrumbCollection
	{
		return $this->crumbs;
	}
}
