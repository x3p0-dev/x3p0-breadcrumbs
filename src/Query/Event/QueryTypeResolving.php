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

use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Packages\Event\BroadcastableEvent;
use X3P0\Breadcrumbs\Packages\Event\BroadcastsToHooks;
use X3P0\Breadcrumbs\Packages\Event\Named;
use X3P0\Breadcrumbs\Packages\Event\NamedEvent;
use X3P0\Breadcrumbs\Packages\Event\Stoppable;
use X3P0\Breadcrumbs\Packages\Event\StoppableEvent;
use X3P0\Breadcrumbs\Query\QueryDefinition;

/**
 * Dispatched while resolving which query type matches the current request,
 * before the query runs. Carries the type detected from the request — a
 * {@see Query} class-string, a {@see QueryDefinition} enum, or null when
 * nothing matched — along with the active {@see BreadcrumbsConfig}, so
 * listeners can inspect the config in play, then change the type by
 * reassigning `$queryType`. The dispatcher returns this same instance and the
 * resolver reads the final value back from it.
 */
final class QueryTypeResolving implements BroadcastableEvent, NamedEvent, StoppableEvent
{
	use BroadcastsToHooks;
	use Named;
	use Stoppable;

	/**
	 * The name of the WordPress action this event is bridged to after it
	 * is dispatched, so `add_action()` callbacks can change the resolved
	 * query type alongside the typed listeners.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const NAME = 'x3p0/breadcrumbs/query-type-resolving';

	/**
	 * Stores the query type resolved so far and the active config. The
	 * query type is mutable so listeners can override it; pass a
	 * {@see QueryDefinition} or {@see Query} class-string. A null value
	 * means no type has been resolved and no breadcrumbs will be built.
	 */
	public function __construct(
		public QueryDefinition|string|null $queryType,
		public readonly BreadcrumbsConfig $breadcrumbsConfig
	) {}
}
