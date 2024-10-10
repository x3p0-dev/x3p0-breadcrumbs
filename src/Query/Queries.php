<?php

/**
 * Queries collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use TypeError;
use X3P0\Breadcrumbs\Contracts;

class Queries implements Contracts\Queries
{
	/**
	 * Stores the array of query classes.
	 */
	protected array $queries = [];

	/**
	 * Allows registering a default set of queries.
	 */
	public function __construct(array $queries = [])
	{
		foreach ($queries as $name => $class) {
			$this->add($name, $class);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(string $name, string $query): void
	{
		if (! is_subclass_of($query, Contracts\Query::class)) {
			throw new TypeError(sprintf(
				__('Only %s classes can be registered', 'x3p0-ideas'),
				Contracts\Query::class
			));
		}

		$this->queries[$name] = $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		unset($this->queries[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return isset($this->queries[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name): ?string
	{
		return $this->has($name) ? $this->queries[$name] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function resolve(string $name, array $params = []): ?Contracts\Query
	{
		$query = $this->get($name);
		return $query ? new $query(...$params) : null;
	}
}
