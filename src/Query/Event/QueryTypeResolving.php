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
use X3P0\Breadcrumbs\Query\QueryDefinition;

/**
 * Dispatched while resolving which query type matches the current request,
 * before the query runs. Carries the type detected from the request — a
 * {@see Query} class-string, a {@see QueryDefiniton} enum, a valid `slug` value
 * at the time of tagging ({@see QueryServiceProvider::register()}), or
 * null when nothing matched — along with the shared context, so listeners can
 * inspect what is being built and the active config, then change the type with
 * `setQueryType()`. The dispatcher returns this same instance and the resolver
 * reads the final value back from it.
 */
final class QueryTypeResolving implements StoppableEvent
{
	use Stoppable;

	/**
	 * The name of the WordPress action this event is bridged to after it
	 * is dispatched, so `add_action()` callbacks can change the resolved
	 * query type alongside the typed listeners.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const HOOK_NAME = 'x3p0/breadcrumbs/query-type-resolving';

	/**
	 * Stores the shared context and the query type resolved so far. The
	 * query type is mutable so listeners can override it; pass a
	 * {@see QueryDefiniton}, {@see Query} class-string, or a tagged query
	 * slug. A null value means no type has been resolved and no breadcrumbs
	 * will be built.
	 */
	public function __construct(
		public readonly BreadcrumbsContext $context,
		private QueryDefinition|string|null $queryType
	) {}

	/**
	 * Returns the query type: a `QueryDefinition`, class-string, a tagged
	 * slug, or null when none matched.
	 */
	public function getQueryType(): QueryDefinition|string|null
	{
		return $this->queryType;
	}

	/**
	 * Overrides the query type to build for. Pass a `QueryDefinition`,
	 * class-string, a tagged slug, or null to build no breadcrumbs.
	 */
	public function setQueryType(QueryDefinition|string|null $queryType): void
	{
		$this->queryType = $queryType;
	}
}
