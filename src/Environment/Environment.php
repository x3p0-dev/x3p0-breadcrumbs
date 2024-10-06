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
use X3P0\Breadcrumbs\Tools\Collection;
use X3P0\Breadcrumbs\{Build, Crumb, Query};

class Environment implements Contracts\Environment
{
	protected Collection $queries;
	protected Collection $builders;
	protected Collection $crumbs;

	public function __construct()
	{
		$this->queries  = new Collection();
		$this->builders = new Collection();
		$this->crumbs   = new Collection();

		$this->registerDefaultQueries();
		$this->registerDefaultBuilders();
		$this->registerDefaultCrumbs();
	}

	public function addQuery(string $name, string $query): void
	{
		if (is_subclass_of(Contracts\Query::class, $query)) {
			$this->queries->add($name, $query);
		}
	}

	public function addBuilder(string $name, string $builder): void
	{
		if (is_subclass_of(Contracts\Build::class, $builder)) {
			$this->builders->add($name, $builder);
		}
	}

	public function addCrumb(string $name, string $crumb): void
	{
		if (is_subclass_of(Contracts\Crumb::class, $crumb)) {
			$this->crumbs->add($name, $crumb);
		}
	}

	public function getQuery(string $name): ?string
	{
		return $this->queries->has($name) ? $this->queries->get($name) : null;
	}

	public function getBuilder(string $name): ?string
	{
		return $this->builders->has($name) ? $this->builders->get($name) : null;
	}

	public function getCrumb(string $name): ?string
	{
		return $this->crumbs->has($name) ? $this->crumbs->get($name) : null;
	}

	public function hasQuery(string $name): bool
	{
		return $this->queries->has($name);
	}

	public function hasBuilder(string $name): bool
	{
		return $this->builders->has($name);
	}

	public function hasCrumb(string $name): bool
	{
		return $this->crumbs->has($name);
	}

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
			$this->queries->add($name, $class);
		}

		do_action('x3p0/breadcrumbs/queries', $this->queries);
	}

	private function registerDefaultBuilders(): void
	{
		$defaults = [
			'home'             => Build\Home::class,
			'map-rewrite-tags' => Build\MapRewriteTags::class,
			'network'          => Build\Network::class,
			'paged'            => Build\Paged::class,
			'path'             => Build\Path::class,
			'post'             => Build\Post::class,
			'post-ancestors'   => Build\PostAncestors::class,
			'post-hierarchy'   => Build\PostHierarchy::class,
			'post-terms'       => Build\PostTerms::class,
			'post-type'        => Build\PostType::class,
			'rewrite-front'    => Build\RewriteFront::class,
			'term'             => Build\Term::class,
			'term-ancestors'   => Build\TermAncestors::class
		];

		foreach ($defaults as $name => $class) {
			$this->builders->add($name, $class);
		}

		do_action('x3p0/breadcrumbs/builders', $this->builders);
	}

	private function registerDefaultCrumbs(): void
	{
		$defaults = [
			'archive'           => Crumb\Archive::class,
			'author'            => Crumb\Author::class,
			'day'               => Crumb\Day::class,
			'error-404'         => Crumb\Error::class,
			'home'              => Crumb\Home::class,
			'minute'            => Crumb\Minute::class,
			'minute-hour'       => Crumb\MinuteHour::class,
			'month'             => Crumb\Month::class,
			'network'           => Crumb\Network::class,
			'network-site'      => Crumb\NetworkSite::class,
			'paged'             => Crumb\Paged::class,
			'paged-comments'    => Crumb\PagedComments::class,
			'paged-singular'    => Crumb\PagedSingular::class,
			'post'              => Crumb\Post::class,
			'post-type'         => Crumb\PostType::class,
			'search'            => Crumb\Search::class,
			'term'              => Crumb\Term::class,
			'week'              => Crumb\Week::class,
			'year'              => Crumb\Year::class
		];

		foreach ($defaults as $name => $class) {
			$this->crumbs->add($name, $class);
		}

		do_action('x3p0/breadcrumbs/crumbs', $this->crumbs);
	}
}
