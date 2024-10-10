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
	 * Returns a query collection.
	 */
	public function queries(): Queries;

	/**
	 * Returns a builder collection.
	 */
	public function builders(): Builders;

	/**
	 * Returns a crumb collection.
	 */
	public function crumbs(): Crumbs;
}
