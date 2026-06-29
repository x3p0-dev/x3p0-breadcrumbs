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
 * override it: first by dispatching a `QueryTypeResolving` event, then through
 * the legacy `x3p0/breadcrumbs/resolve/query-type` filter, which keeps the final
 * say for backward compatibility. The result is a query key string (a built-in
 * `QueryType` value or a custom one) or null when nothing matched.
 */
final class QueryResolver
{
	/**
	 * Maps WordPress conditional tags to the `QueryType` used when that
	 * conditional matches the current request. Evaluated in order; the
	 * first match wins.
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

		// Normalize the event's value to a string key for the legacy
		// filter and the return type.
		$queryType = $event->getQueryType();
		$key = $queryType instanceof QueryType ? $queryType->value : $queryType;

		// Apply the legacy filter last so existing callbacks keep the
		// final say, preserving backward compatibility.
		// @deprecated 5.0.0
		return apply_filters('x3p0/breadcrumbs/resolve/query-type', $key);
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
