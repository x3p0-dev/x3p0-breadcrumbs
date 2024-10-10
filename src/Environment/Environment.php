<?php

/**
 * Primary environment implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs\Environment;

use X3P0\Breadcrumbs\Contracts;
use X3P0\Breadcrumbs\{Builder, Crumb, Query};
use X3P0\Breadcrumbs\Contracts\Queries;

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
	 */
	protected Contracts\Queries $queries;

	/**
	 * Houses a collection where the keys are the builder name and the values
	 * are the class names for implementing the `Builder` interface.
	 */
	protected Contracts\Builders $builders;

	/**
	 * Houses a collection where the keys are the crumb name and the values
	 * are the class names for implementing the `Crumb` interface.
	 */
	protected Contracts\Crumbs $crumbs;

	/**
	 * Builds a new environment by creating empty collections for queries,
	 * builders, and crumbs. It then registers the defaults.
	 */
	public function __construct(
		array $queries = [],
		array $builders = [],
		array $crumbs = []
	) {
		$this->queries  = new Query\Queries($queries);
		$this->builders = new Builder\Builders($builders);
		$this->crumbs   = new Crumb\Crumbs($crumbs);

		$this->registerDefaultQueries();
		$this->registerDefaultBuilders();
		$this->registerDefaultCrumbs();

		do_action('x3p0/breadcrumbs/environment', $this);
	}

	/**
	 * {@inheritdoc}
	 */
	public function queries(): Contracts\Queries
	{
		return $this->queries;
	}

	/**
	 * {@inheritdoc}
	 */
	public function builders(): Contracts\Builders
	{
		return $this->builders;
	}

	/**
	 * {@inheritdoc}
	 */
	public function crumbs(): Contracts\Crumbs
	{
		return $this->crumbs;
	}

	/**
	 * Registers the default query classes with the environment.
	 */
	private function registerDefaultQueries(): void
	{
		$defaults = [
			'archive'           => Query\Archive::class,
			'author'            => Query\Author::class,
			'day'               => Query\Day::class,
			'error-404'         => Query\Error::class,
			'front-page'        => Query\FrontPage::class,
			'home'              => Query\Home::class,
			'hour'              => Query\Hour::class,
			'minute'            => Query\Minute::class,
			'minute-hour'       => Query\MinuteHour::class,
			'month'             => Query\Month::class,
			'paged'             => Query\Paged::class,
			'post-type-archive' => Query\PostTypeArchive::class,
			'search'            => Query\Search::class,
			'singular'          => Query\Singular::class,
			'taxonomy'          => Query\Tax::class,
			'week'              => Query\Week::class,
			'year'              => Query\Year::class
		];

		foreach ($defaults as $name => $class) {
			if (! $this->queries()->has($name)) {
				$this->queries()->add($name, $class);
			}
		}
	}

	/**
	 * Registers the default builder classes with the environment.
	 */
	private function registerDefaultBuilders(): void
	{
		$defaults = [
			'home'              => Builder\Home::class,
			'network'           => Builder\Network::class,
			'paged'             => Builder\Paged::class,
			'path'              => Builder\Path::class,
			'post'              => Builder\Post::class,
			'post-ancestors'    => Builder\PostAncestors::class,
			'post-hierarchy'    => Builder\PostHierarchy::class,
			'post-rewrite-tags' => Builder\PostRewriteTags::class,
			'post-terms'        => Builder\PostTerms::class,
			'post-type'         => Builder\PostType::class,
			'rewrite-front'     => Builder\RewriteFront::class,
			'term'              => Builder\Term::class,
			'term-ancestors'    => Builder\TermAncestors::class
		];

		foreach ($defaults as $name => $class) {
			if (! $this->builders()->has($name)) {
				$this->builders()->add($name, $class);
			}
		}
	}

	/**
	 * Registers the default crumb classes with the environment.
	 */
	private function registerDefaultCrumbs(): void
	{
		$defaults = [
			'archive'        => Crumb\Archive::class,
			'author'         => Crumb\Author::class,
			'day'            => Crumb\Day::class,
			'error-404'      => Crumb\Error::class,
			'home'           => Crumb\Home::class,
			'minute'         => Crumb\Minute::class,
			'minute-hour'    => Crumb\MinuteHour::class,
			'month'          => Crumb\Month::class,
			'network'        => Crumb\Network::class,
			'network-site'   => Crumb\NetworkSite::class,
			'paged'          => Crumb\Paged::class,
			'paged-comments' => Crumb\PagedComments::class,
			'paged-singular' => Crumb\PagedSingular::class,
			'post'           => Crumb\Post::class,
			'post-type'      => Crumb\PostType::class,
			'search'         => Crumb\Search::class,
			'term'           => Crumb\Term::class,
			'week'           => Crumb\Week::class,
			'year'           => Crumb\Year::class
		];

		foreach ($defaults as $name => $class) {
			if (! $this->crumbs()->has($name)) {
				$this->crumbs()->add($name, $class);
			}
		}
	}
}
