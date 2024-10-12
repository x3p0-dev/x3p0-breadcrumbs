<?php

/**
 * Query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Contracts;

/**
 * Implements the `Query` interface and creates a custom query object.
 */
abstract class Query implements Contracts\Query
{
	/**
	 * Creates a new query object.
	 */
	public function __construct(protected Contracts\Builder $builder)
	{}
}
