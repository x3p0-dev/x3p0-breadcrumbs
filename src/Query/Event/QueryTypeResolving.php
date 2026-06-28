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

/**
 * Dispatched while resolving which query type matches the current request,
 * before the query runs. Carries the type detected from the request (a query
 * key string, or null when nothing matched) along with the shared context, so
 * listeners can inspect what is being built and the active config, then change
 * the type with `setQueryType()`. The dispatcher returns this same instance and
 * the resolver reads the final value back from it.
 */
final class QueryTypeResolving
{
	/**
	 * Stores the shared context for the build and the query type resolved
	 * so far. The query type is mutable so listeners can override it; a null
	 * value means no query type has been resolved and no trail will be built.
	 */
	public function __construct(
		public readonly BreadcrumbsContext $context,
		private ?string $queryType
	) {}

	/**
	 * Returns the query type key resolved so far, or null when none matched.
	 */
	public function getQueryType(): ?string
	{
		return $this->queryType;
	}

	/**
	 * Overrides the query type key to build the trail for. Pass null to build
	 * no trail.
	 */
	public function setQueryType(?string $queryType): void
	{
		$this->queryType = $queryType;
	}
}
