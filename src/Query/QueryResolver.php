<?php

/**
 * Query resolver class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Packages\Event\Dispatcher;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

/**
 * Decides which query type matches the current WordPress request. It detects a
 * default type from an ordered map of conditional tags, then lets third parties
 * override it in turn: first by dispatching a {@see QueryTypeResolving} event,
 * then by firing the {@see QueryTypeResolving::HOOK_NAME} action so `add_action()`
 * callbacks can change the same event. A listener that stops the event's
 * propagation claims the final say early, skipping the action. The result is a
 * {@see Query} class-string, {@see QueryDefinition} enum, or null.
 */
final class QueryResolver
{
	/**
	 * Stores the dispatcher used to broadcast the resolution event.
	 */
	public function __construct(private readonly Dispatcher $events)
	{}

	/**
	 * Resolves the query type for the current request, giving listeners
	 * a chance to override the detected default.
	 */
	public function resolve(BreadcrumbsContext $context): QueryType|string|null
	{
		// Let listeners inspect the context and change the detected
		// type. The event accepts a `QueryType` case or a class-string.
		$event = $this->events->dispatch(new QueryTypeResolving(
			context:   $context,
			queryType: $this->detect()
		));

		// Bridge the event to WordPress unless a listener already
		// stopped propagation, so `add_action()` callbacks can change
		// the type alongside the typed listeners.
		if (! $event->isPropagationStopped()) {
			do_action(QueryTypeResolving::HOOK_NAME, $event);
		}

		// Get the query type from the event.
		return $event->getQueryType();
	}

	/**
	 * Returns the `QueryType` mapped to the first matching WordPress
	 * conditional, or null if none match.
	 */
	private function detect(): ?QueryType
	{
		return match (true) {
			is_404()        => QueryType::Error404,
			is_front_page() => QueryType::FrontPage,
			is_home()       => QueryType::Home,
			is_singular()   => QueryType::Singular,
			is_archive()    => QueryType::Archive,
			is_search()     => QueryType::Search,
			default         => null
		};
	}
}
