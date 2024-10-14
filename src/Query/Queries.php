<?php

/**
 * Queries collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

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
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-ideas'),
				Contracts\Query::class
			)));
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
	public function get(string $name, array $params = []): ?Contracts\Query
	{
		if ($this->has($name)) {
			$query = $this->queries[$name];
			return new $query(...$params);
		}

		return null;
	}
}
