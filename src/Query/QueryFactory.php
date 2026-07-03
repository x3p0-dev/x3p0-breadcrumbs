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

/**
 * Resolves a query key to its registered class and instantiates it. Returns
 * `null` when the key is not registered, so callers can dispatch optimistically
 * without first checking the registry.
 */
final class QueryFactory
{
	/**
	 * Stores the registry that maps query keys to their class names.
	 */
	public function __construct(private readonly QueryRegistry $queryRegistry)
	{}

	/**
	 * Instantiates the query registered under `$type`, spreading `$params` as
	 * named constructor arguments. Accepts a `QueryType` for built-in queries or
	 * a string key for custom ones. Returns `null` if the key is not registered.
	 */
	public function make(QueryType|string $type, array $params = []): ?Query
	{
		$key = $type instanceof QueryType ? $type->value : $type;

		if ($query = $this->queryRegistry->get($key)) {
			return new $query(...$params);
		}

		return null;
	}
}
