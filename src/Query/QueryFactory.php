<?php

/**
 * Query factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadquerys
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Contracts;

class QueryFactory implements Contracts\QueryFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(
		private Contracts\QueryTypeRegistry $queryTypes
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function make(string $name, array $params = []): ?Contracts\Query
	{
		if ($this->queryTypes->has($name)) {
			$query = $this->queryTypes->get($name);
			return new $query(...$params);
		}

		return null;
	}
}
