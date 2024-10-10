<?php

/**
 * Queries interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Queries is meant for storing a registry of `Query` classes.
 */
interface Queries
{
	/**
	 * Add a query.
	 *
	 * @param class-string<Query> $query
	 */
	public function add(string $name, string $query): void;

	/**
	 * Removes a query.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if a query is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Returns a query or `null`.
	 *
	 * @return null|class-string<Query>
	 */
	public function get(string $name): ?string;

	/**
	 * Resolves a query implementation.
	 */
	public function resolve(string $name, array $params = []): ?Query;
}