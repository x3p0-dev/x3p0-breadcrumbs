<?php

/**
 * Query registry class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use TypeError;

/**
 * Registry class for storing query classes.
 */
final class QueryRegistry
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
		foreach ($queries as $key => $queryClass) {
			$this->register($key, $queryClass);
		}
	}

	/**
	 * Registers a query class.
	 *
	 * @param class-string<Query> $queryClass
	 */
	public function register(string $key, string $queryClass): void
	{
		if (! is_subclass_of($queryClass, Query::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-breadcrumbs'),
				Query::class
			)));
		}

		$this->queries[$key] = $queryClass;
	}

	/**
	 * Unregisters a query class.
	 */
	public function unregister(string $key): void
	{
		unset($this->queries[$key]);
	}

	/**
	 * Checks if a query is registered.
	 */
	public function isRegistered(string $key): bool
	{
		return isset($this->queries[$key]);
	}

	/**
	 * Returns a query class string or `null`.
	 *
	 * @return null|class-string<Query>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->queries[$key] : null;
	}
}
