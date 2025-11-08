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
use X3P0\Breadcrumbs\Crumb\{CrumbCollection, CrumbFactory};
use X3P0\Breadcrumbs\Query\QueryFactory;

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
		private CrumbCollection   $crumbs,
		private QueryFactory      $queryFactory,
		private AssemblerFactory  $assemblerFactory,
		private CrumbFactory      $crumbFactory,
		private BreadcrumbsConfig $config
	) {}

	/**
	 * Run a query by key.
	 */
	public function query(string $key, array $params = []): void
	{
		$query = $this->queryFactory->make($key, [
			'context' => $this,
			...$params
		]);

		$query?->query();
	}

	/**
	 * Run an assembler by key.
	 */
	public function assemble(string $key, array $params = []): void
	{
		$assembler = $this->assemblerFactory->make($key, [
			'context' => $this,
			...$params
		]);

		$assembler?->assemble();
	}

	/**
	 * Add a crumb by key.
	 */
	public function addCrumb(string $key, array $params = []): void
	{
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
