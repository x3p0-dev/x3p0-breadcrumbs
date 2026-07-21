<?php

/**
 * Query factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\DeferTaggedWith;
use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Resolves a query type to a container identifier and builds it. A `QueryType`
 * (the `QueryType` enum or a third-party implementation) or its string key
 * resolves to the query's container alias; a class name is built directly.
 * Returns `null` for an unknown key so callers can dispatch optimistically.
 */
final class QueryFactory
{
	/**
	 * Stores the resolver that builds the mapped class through the container.
	 */
	public function __construct(
		#[DeferTaggedWith(Query::TAG, 'slug')] private readonly array $factories,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the query for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 */
	public function make(QueryDefinition|string $abstract, array $params = []): ?Query
	{
		$abstract = is_string($abstract) ? $abstract : $abstract->value;

		// If passing a class string, we can just resolve directly.
		if (is_subclass_of($abstract, Query::class)) {
			/** @var null|Query */
			return $this->resolver->make($abstract, $params);
		}

		/** @var null|Query */
		return isset($this->factories[$abstract]) ? ($this->factories[$abstract])($params) : null;
	}
}
