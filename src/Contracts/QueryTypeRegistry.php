<?php

/**
 * Query type interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Stores a registry of query types.
 */
interface QueryTypeRegistry
{
	/**
	 * Add a query type.
	 *
	 * @param class-string<Query> $query
	 */
	public function add(string $name, string $query): void;

	/**
	 * Removes a query type.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if a query type is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Resolves and returns a query object or `null`.
	 */
	public function get(string $name, array $params = []): ?Query;
}
