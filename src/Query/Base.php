<?php

/**
 * Query class.
 *
 * This is the base class, which should be sub-classed, for building breadcrumbs
 * based on the current query. Each query class is based on the current WP main
 * query. Each class can call another query class, one or more build classes, or
 * one or more crumb classes.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Query;

abstract class Base implements Query
{
	/**
	 * Creates a new query object. Any data passed in within the `$data`
	 * array will be automatically assigned to any existing properties, which
	 * can be useful for sub-classes that have custom properties.
	 */
	public function __construct(protected Breadcrumbs $breadcrumbs)
	{}

	/**
	 * A method for building the breadcrumbs based on the query. Generally,
	 * this should be used to call `$this->breadcrumbs->build()` to call
	 * crumb builders or `$this->breadcrumbs->crumb()` to add a crumbs to
	 * the breadcrumb trail.
	 */
	abstract public function make(): void;
}
