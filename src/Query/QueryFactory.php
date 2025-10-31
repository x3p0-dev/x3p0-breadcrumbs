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

/**
 * Factory class used for creating new query instances from a registry of
 * query types.
 */
final class QueryFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(private QueryRegistry $queryTypes)
	{}

	/**
	 * Creates an instance of a query object.
	 */
	public function make(string $name, array $params = []): ?Query
	{
		if ($this->queryTypes->isRegistered($name)) {
			$query = $this->queryTypes->get($name);
			return new $query(...$params);
		}

		return null;
	}
}
