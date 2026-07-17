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

use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Resolves a query key to its registered class and instantiates it. Returns
 * `null` when the key is not registered, so callers can dispatch optimistically
 * without first checking the registry.
 */
final class QueryFactory
{
	/**
	 * Stores the registry that maps query keys to their class names and the
	 * resolver that builds the mapped class through the container.
	 */
	public function __construct(
		private readonly QueryRegistry    $queryRegistry,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Resolves the query registered under `$type` from the container,
	 * forwarding `$params` as named constructor arguments. Accepts a
	 * `QueryType` case, one of the concrete `Type\` class names it defines,
	 * or a string key (built-in or custom). Returns `null` if the resolved
	 * key is not registered.
	 */
	public function make(QueryType|string $type, array $params = []): ?Query
	{
		$key = QueryType::key($type);

		/** @var null|class-string<Query> $query */
		if ($query = $this->queryRegistry->get($key)) {
			return $this->resolver->make($query, $params);
		}

		return null;
	}
}
