<?php

/**
 * Breadcrumbs environment interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * The environment contract is a container for storing queries, builders, and
 * crumbs, which are the building blocks for creating a breadcrumb trail.
 */
interface Environment
{
	/**
	 * Adds a query to the environment. The `$query` parameter is expected
	 * to be a class that implements the `Query` interface.
	 *
	 * @param class-string<Query> $query
	 */
	public function addQuery(string $name, string $query): void;

	/**
	 * Adds a builder to the environment. The `$builder` parameter is expected
	 * to be a class that implements the `Builder` interface.
	 *
	 * @param class-string<Build> $builder
	 */
	public function addBuilder(string $name, string $builder): void;

	/**
	 * Adds a crumb to the environment. The `$crumb` parameter is expected
	 * to be a class that implements the `Crumb` interface.
	 *
	 * @param class-string<Crumb> $crumb
	 */
	public function addCrumb(string $name, string $crumb): void;

	/**
	 * Returns a query class name that implements the `Query` interface or
	 * `null` if the query is not registered.
	 *
	 * @return null|class-string<Query>
	 */
	public function getQuery(string $name): ?string;

	/**
	 * Returns a builder class name that implements the `Builder` interface
	 * or `null` if the builder is not registered.
	 *
	 * @return null|class-string<Build>
	 */
	public function getBuilder(string $name): ?string;

	/**
	 * Returns a crumb class name that implements the `Crumb` interface or
	 * `null` if the crumb is not registered.
	 *
	 * @return null|class-string<Crumb>
	 */
	public function getCrumb(string $name): ?string;

	/**
	 * Conditional check to determine if a query is registered.
	 */
	public function hasQuery(string $name): bool;

	/**
	 * Conditional check to determine if a builder is registered.
	 */
	public function hasBuilder(string $name): bool;

	/**
	 * Conditional check to determine if a crumb is registered.
	 */
	public function hasCrumb(string $name): bool;
}
