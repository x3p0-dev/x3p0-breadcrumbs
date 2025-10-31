<?php

/**
 * Primary environment implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Environment;

use X3P0\Breadcrumbs\Contracts;
use X3P0\Breadcrumbs\Assembler\{
	Assembler,
	AssemblerFactory,
	AssemblerRegistrar,
	AssemblerRegistry
};
use X3P0\Breadcrumbs\Crumb\{
	Crumb,
	CrumbFactory,
	CrumbRegistrar,
	CrumbRegistry
};
use X3P0\Breadcrumbs\Query\{
	Query,
	QueryFactory,
	QueryRegistrar,
	QueryRegistry
};

/**
 * The default implementation of the `Environment` interface. It is the backbone
 * of generating breadcrumbs and is responsible for setting up the default query,
 * builder, and crumb classes.
 */
class Environment implements Contracts\Environment
{
	/**
	 * Houses a collection where the keys are the query name and the values
	 * are the class names for implementing the `Query` interface.
	 *
	 * @todo Make public with property hooks with minimum PHP 8.4 requirement.
	 */
	protected QueryRegistry $queryRegistry;

	/**
	 * Houses a collection where the keys are the builder name and the values
	 * are the class names for implementing the `Assembler` interface.
	 *
	 * @todo Make public with property hooks with minimum PHP 8.4 requirement.
	 */
	protected AssemblerRegistry $assemblerRegistry;

	/**
	 * Houses a collection where the keys are the crumb name and the values
	 * are the class names for implementing the `Crumb` interface.
	 *
	 * @todo Make public with property hooks with minimum PHP 8.4 requirement.
	 */
	protected CrumbRegistry $crumbRegistry;

	/**
	 * Factory for creating query instances.
	 */
	protected QueryFactory $queryFactory;

	/**
	 * Factory for creating assembler instances.
	 */
	protected AssemblerFactory $assemblerFactory;

	/**
	 * Factory for creating crumb instances.
	 */
	protected CrumbFactory $crumbFactory;

	/**
	 * Builds a new environment by creating registries and factories, then
	 * registers the defaults.
	 */
	public function __construct(
		array $queryTypes = [],
		array $assemblerTypes = [],
		array $crumbTypes = []
	) {
		// Initialize registries.
		$this->queryRegistry     = new QueryRegistry($queryTypes);
		$this->assemblerRegistry = new AssemblerRegistry($assemblerTypes);
		$this->crumbRegistry     = new CrumbRegistry($crumbTypes);

		// Register the default types.
		QueryRegistrar::register($this->queryRegistry);
		AssemblerRegistrar::register($this->assemblerRegistry);
		CrumbRegistrar::register($this->crumbRegistry);

		// Allow developers to hook into the environment and customize.
		do_action('x3p0/breadcrumbs/environment', $this);

		// Initialize factories with their respective registries.
		$this->queryFactory     = new QueryFactory($this->queryRegistry);
		$this->assemblerFactory = new AssemblerFactory($this->assemblerRegistry);
		$this->crumbFactory     = new CrumbFactory($this->crumbRegistry);
	}

	/**
	 * {@inheritDoc}
	 */
	public function queryRegistry(): QueryRegistry
	{
		return $this->queryRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function assemblerRegistry(): AssemblerRegistry
	{
		return $this->assemblerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function crumbRegistry(): CrumbRegistry
	{
		return $this->crumbRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeQuery(string $name, array $params = []): ?Query
	{
		return $this->queryFactory->make($name, $params);
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeAssembler(string $name, array $params = []): ?Assembler
	{
		return $this->assemblerFactory->make($name, $params);
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeCrumb(string $name, array $params = []): ?Crumb
	{
		return $this->crumbFactory->make($name, $params);
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function getQueries(): QueryRegistry
	{
		return $this->queryRegistry();
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function getAssemblers(): AssemblerRegistry
	{
		return $this->assemblerRegistry();
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function getCrumbs(): CrumbRegistry
	{
		return $this->crumbRegistry();
	}
}
