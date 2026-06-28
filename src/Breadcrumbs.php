<?php

/**
 * Breadcrumbs implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Query\QueryFactory;
use X3P0\Breadcrumbs\Query\QueryResolver;

/**
 * Builds a breadcrumb trail for the current request. It delegates to the
 * `QueryResolver` to detect which query matches the current WordPress request,
 * hands off to the query/assembler/crumb pipeline (via a `BreadcrumbsContext`),
 * and returns the accumulated crumb collection. This is the build half of the
 * breadcrumbs flow; rendering the collection to markup is handled separately by
 * `BreadcrumbsRenderer`.
 */
final class Breadcrumbs
{
	/**
	 * Sets up the build with the query resolver, the factories used to create
	 * the pipeline participants, and the config that controls how the trail is
	 * built.
	 */
	public function __construct(
		private readonly QueryResolver     $queryResolver,
		private readonly QueryFactory      $queryFactory,
		private readonly AssemblerFactory  $assemblerFactory,
		private readonly CrumbFactory      $crumbFactory,
		private readonly BreadcrumbsConfig $config
	) {}

	/**
	 * Builds and returns the crumb collection for the current request.
	 * Creates the shared context, resolves the matching query type (which
	 * third parties can override), runs that query to populate the trail, and
	 * returns the result.
	 */
	public function generate(): CrumbCollection
	{
		// Create the shared context passed through the query, assembler,
		// and crumb pipeline.
		$context = new BreadcrumbsContext(
			crumbs:           new CrumbCollection(),
			queryFactory:     $this->queryFactory,
			assemblerFactory: $this->assemblerFactory,
			crumbFactory:     $this->crumbFactory,
			config:           $this->config
		);

		// Resolve the query type for the request, then run it to build the
		// trail. Resolution is overridable via the `QueryTypeResolving` event
		// and the legacy filter.
		$queryType = $this->queryResolver->resolve($context);

		if ($queryType) {
			$context->query($queryType);
		}

		return $context->crumbs();
	}
}
