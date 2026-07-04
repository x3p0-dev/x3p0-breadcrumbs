<?php

/**
 * Query type resolving event.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Event;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Packages\Event\Stoppable;
use X3P0\Breadcrumbs\Packages\Event\StoppableEvent;
use X3P0\Breadcrumbs\Query\QueryType;

/**
 * Dispatched while resolving which query type matches the current request,
 * before the query runs. Carries the type detected from the request — a
 * `QueryType` case for a built-in type, a string key for a custom one, or null
 * when nothing matched — along with the shared context, so listeners can inspect
 * what is being built and the active config, then change the type with
 * `setQueryType()`. The dispatcher returns this same instance and the resolver
 * reads the final value back from it.
 */
final class QueryTypeResolving implements StoppableEvent
{
	use Stoppable;

	/**
	 * Stores the shared context and the query type resolved so far. The
	 * query type is mutable so listeners can override it; pass a `QueryType`
	 * case for a built-in type or a string key for a custom one. A null
	 * value means no type has been resolved and no breadcrumbs will be built.
	 */
	public function __construct(
		public readonly BreadcrumbsContext $context,
		private QueryType|string|null $queryType
	) {}

	/**
	 * Returns the query type resolved so far: a `QueryType` case, a string
	 * key, or null when none matched.
	 */
	public function getQueryType(): QueryType|string|null
	{
		return $this->queryType;
	}

	/**
	 * Overrides the query type to build for. Pass a `QueryType` case, a
	 * string key for a custom type, or null to build no breadcrumbs.
	 */
	public function setQueryType(QueryType|string|null $queryType): void
	{
		$this->queryType = $queryType;
	}
}
