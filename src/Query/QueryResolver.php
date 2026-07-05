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
 * override it in turn: first by dispatching a `QueryTypeResolving` event, then by
 * firing the `x3p0/breadcrumbs/query-type-resolving` action so `add_action()`
 * callbacks can change the same event, and finally through the legacy
 * `x3p0/breadcrumbs/resolve/query-type` filter, which keeps the final say for
 * backward compatibility. A listener that stops the event's propagation claims
 * the final say early, skipping the action and the legacy filter. The result is
 * a query key string (a built-in `QueryType` value or a custom one) or null when
 * nothing matched.
 */
final class QueryResolver
{
	/**
	 * Maps WordPress conditional tags to the `QueryType` used when that
	 * conditional matches the current request. Evaluated in order; the
	 * first match wins.
	 *
	 * @var  array<string, QueryType>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const CONDITIONALS = [
		'is_404'        => QueryType::Error404,
		'is_front_page' => QueryType::FrontPage,
		'is_home'       => QueryType::Home,
		'is_singular'   => QueryType::Singular,
		'is_archive'    => QueryType::Archive,
		'is_search'     => QueryType::Search
	];

	/**
	 * Stores the dispatcher used to broadcast the resolution event.
	 */
	public function __construct(private readonly Dispatcher $events)
	{}

	/**
	 * Resolves the query type key for the current request, giving listeners
	 * and the legacy filter a chance to override the detected default.
	 */
	public function resolve(BreadcrumbsContext $context): ?string
	{
		// Let listeners inspect the context and change the detected
		// type. The event accepts a `QueryType` case or a string key.
		$event = $this->events->dispatch(new QueryTypeResolving(
			context:   $context,
			queryType: $this->detect()
		));

		// Bridge the event to WordPress unless a listener already claimed
		// the final say by stopping propagation, so `add_action()`
		// callbacks can change the type alongside the typed listeners.
		if (! $event->isPropagationStopped()) {
			do_action('x3p0/breadcrumbs/query-type-resolving', $event);
		}

		// Normalize the event's value to a string key for the legacy
		// filter and the return type.
		$queryType = $event->getQueryType();
		$key = $queryType instanceof QueryType ? $queryType->value : $queryType;

		// A listener that stopped propagation — typed or through the hook —
		// has claimed the final say, so skip the legacy filter and return
		// its decision as-is.
		if ($event->isPropagationStopped()) {
			return $key;
		}

		// Apply the legacy filter last so existing callbacks keep the
		// final say, preserving backward compatibility. The deprecation
		// notice steers integrators toward the `QueryTypeResolving` event.
		return apply_filters_deprecated(
			'x3p0/breadcrumbs/resolve/query-type',
			[ $key ],
			'5.0.0',
			QueryTypeResolving::class
		);
	}

	/**
	 * Returns the `QueryType` mapped to the first matching WordPress
	 * conditional, or null if none match.
	 */
	private function detect(): ?QueryType
	{
		foreach (self::CONDITIONALS as $tag => $type) {
			if ($tag()) {
				return $type;
			}
		}

		return null;
	}
}
