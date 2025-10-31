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
use X3P0\Breadcrumbs\{Assembler, Crumb, Query, Query\QueryRegistry};

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
	protected Query\QueryRegistry $queryRegistry;

	/**
	 * Houses a collection where the keys are the builder name and the values
	 * are the class names for implementing the `Assembler` interface.
	 *
	 * @todo Make public with property hooks with minimum PHP 8.4 requirement.
	 */
	protected Assembler\AssemblerRegistry $assemblerRegistry;

	/**
	 * Houses a collection where the keys are the crumb name and the values
	 * are the class names for implementing the `Crumb` interface.
	 *
	 * @todo Make public with property hooks with minimum PHP 8.4 requirement.
	 */
	protected Crumb\CrumbRegistry $crumbRegistry;

	/**
	 * Factory for creating query instances.
	 */
	protected Query\QueryFactory $queryFactory;

	/**
	 * Factory for creating assembler instances.
	 */
	protected Assembler\AssemblerFactory $assemblerFactory;

	/**
	 * Factory for creating crumb instances.
	 */
	protected Crumb\CrumbFactory $crumbFactory;

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
		$this->queryRegistry     = new Query\QueryRegistry($queryTypes);
		$this->assemblerRegistry = new Assembler\AssemblerRegistry($assemblerTypes);
		$this->crumbRegistry     = new Crumb\CrumbRegistry($crumbTypes);

		// Register the default types.
		$this->registerDefaultQueries();
		$this->registerDefaultAssemblers();
		$this->registerDefaultCrumbs();

		// Allow developers to hook into the environment and customize.
		do_action('x3p0/breadcrumbs/environment', $this);

		// Initialize factories with their respective registries.
		$this->queryFactory     = new Query\QueryFactory($this->queryRegistry);
		$this->assemblerFactory = new Assembler\AssemblerFactory($this->assemblerRegistry);
		$this->crumbFactory     = new Crumb\CrumbFactory($this->crumbRegistry);
	}

	/**
	 * {@inheritDoc}
	 */
	public function queryRegistry(): Query\QueryRegistry
	{
		return $this->queryRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function assemblerRegistry(): Assembler\AssemblerRegistry
	{
		return $this->assemblerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function crumbRegistry(): Crumb\CrumbRegistry
	{
		return $this->crumbRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeQuery(string $name, array $params = []): ?Query\Query
	{
		return $this->queryFactory->make($name, $params);
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeAssembler(string $name, array $params = []): ?Assembler\Assembler
	{
		return $this->assemblerFactory->make($name, $params);
	}

	/**
	 * {@inheritDoc}
	 */
	public function makeCrumb(string $name, array $params = []): ?Crumb\Crumb
	{
		return $this->crumbFactory->make($name, $params);
	}

	/**
	 * Registers the default query classes with the environment.
	 */
	private function registerDefaultQueries(): void
	{
		$defaults = [
			'archive'           => Query\Type\Archive::class,
			'author'            => Query\Type\Author::class,
			'day'               => Query\Type\Day::class,
			'error-404'         => Query\Type\Error::class,
			'front-page'        => Query\Type\FrontPage::class,
			'home'              => Query\Type\Home::class,
			'hour'              => Query\Type\Hour::class,
			'minute'            => Query\Type\Minute::class,
			'minute-hour'       => Query\Type\MinuteHour::class,
			'month'             => Query\Type\Month::class,
			'paged'             => Query\Type\Paged::class,
			'post-type-archive' => Query\Type\PostTypeArchive::class,
			'search'            => Query\Type\Search::class,
			'singular'          => Query\Type\Singular::class,
			'taxonomy'          => Query\Type\Tax::class,
			'week'              => Query\Type\Week::class,
			'year'              => Query\Type\Year::class
		];

		foreach ($defaults as $name => $class) {
			if (! $this->queryRegistry()->isRegistered($name)) {
				$this->queryRegistry()->register($name, $class);
			}
		}
	}

	/**
	 * Registers the default builder classes with the environment.
	 */
	private function registerDefaultAssemblers(): void
	{
		$defaults = [
			'home'              => Assembler\Type\Home::class,
			'paged'             => Assembler\Type\Paged::class,
			'path'              => Assembler\Type\Path::class,
			'post'              => Assembler\Type\Post::class,
			'post-ancestors'    => Assembler\Type\PostAncestors::class,
			'post-hierarchy'    => Assembler\Type\PostHierarchy::class,
			'post-rewrite-tags' => Assembler\Type\PostRewriteTags::class,
			'post-terms'        => Assembler\Type\PostTerms::class,
			'post-type'         => Assembler\Type\PostType::class,
			'rewrite-front'     => Assembler\Type\RewriteFront::class,
			'term'              => Assembler\Type\Term::class,
			'term-ancestors'    => Assembler\Type\TermAncestors::class
		];

		foreach ($defaults as $name => $class) {
			if (! $this->assemblerRegistry()->isRegistered($name)) {
				$this->assemblerRegistry()->register($name, $class);
			}
		}
	}

	/**
	 * Registers the default crumb classes with the environment.
	 */
	private function registerDefaultCrumbs(): void
	{
		$defaults = [
			'archive'        => Crumb\Type\Archive::class,
			'author'         => Crumb\Type\Author::class,
			'day'            => Crumb\Type\Day::class,
			'error-404'      => Crumb\Type\Error404::class,
			'home'           => Crumb\Type\Home::class,
			'minute'         => Crumb\Type\Minute::class,
			'minute-hour'    => Crumb\Type\MinuteHour::class,
			'month'          => Crumb\Type\Month::class,
			'network'        => Crumb\Type\Network::class,
			'network-site'   => Crumb\Type\NetworkSite::class,
			'paged'          => Crumb\Type\Paged::class,
			'paged-comments' => Crumb\Type\PagedComments::class,
			'paged-singular' => Crumb\Type\PagedSingular::class,
			'post'           => Crumb\Type\Post::class,
			'post-type'      => Crumb\Type\PostType::class,
			'search'         => Crumb\Type\Search::class,
			'term'           => Crumb\Type\Term::class,
			'week'           => Crumb\Type\Week::class,
			'year'           => Crumb\Type\Year::class
		];

		foreach ($defaults as $name => $class) {
			if (! $this->crumbRegistry()->isRegistered($name)) {
				$this->crumbRegistry()->register($name, $class);
			}
		}
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function getQueries(): Query\QueryRegistry
	{
		return $this->queryRegistry();
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function getAssemblers(): Assembler\AssemblerRegistry
	{
		return $this->assemblerRegistry();
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function getCrumbs(): Crumb\CrumbRegistry
	{
		return $this->crumbRegistry();
	}
}
