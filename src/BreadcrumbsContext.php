<?php

/**
 * Breadcrumbs context.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Query\QueryFactory;

/**
 * Provides a simple API for queries, assemblers, and crumbs to build the crumb
 * collection. Wraps the factories and collection to hide complexity.
 */
class BreadcrumbsContext
{
	public function __construct(
		private CrumbCollection   $crumbs,
		private QueryFactory      $queryFactory,
		private AssemblerFactory  $assemblerFactory,
		private CrumbFactory      $crumbFactory,
		private BreadcrumbsConfig $config
	) {}

	/**
	 * Run a query by name.
	 */
	public function query(string $name, array $params = []): void
	{
		$query = $this->queryFactory->make($name, [
			'context' => $this,
			...$params
		]);

		$query?->query();
	}

	/**
	 * Run an assembler by name.
	 */
	public function assemble(string $name, array $params = []): void
	{
		$assembler = $this->assemblerFactory->make($name, [
			'context' => $this,
			...$params
		]);

		$assembler?->assemble();
	}

	/**
	 * Add a crumb by name.
	 */
	public function addCrumb(string $name, array $params = []): void
	{
		$crumb = $this->crumbFactory->make($name, [
			'context' => $this,
			...$params
		]);

		if ($crumb) {
			$this->crumbs->set($name, $crumb);
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
	public function crumbs(): Crumb\CrumbCollection
	{
		return $this->crumbs;
	}
}
