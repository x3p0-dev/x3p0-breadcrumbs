<?php

/**
 * Query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Query;

/**
 * Implements the `Query` interface and creates a custom query object.
 */
abstract class Base implements Query
{
	/**
	 * Creates a new query object.
	 */
	public function __construct(protected Breadcrumbs $breadcrumbs)
	{}

	/**
	 * {@inheritdoc}
	 */
	abstract public function make(): void;
}
