<?php

/**
 * Query registry class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\InvalidTypeException;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\ClassRegistry;

/**
 * Stores the `string key => Query class name` mappings that the factory
 * resolves against. Registration rejects any class that is not a `Query`
 * subclass, so every stored value is guaranteed instantiable as a query.
 */
final class QueryRegistry implements ClassRegistry
{
	/**
	 * Maps each registered query key to its class name.
	 *
	 * @var array<string, class-string<Query>>
	 */
	private array $queries = [];

	/**
	 * Registers an optional initial set of query classes, keyed by query key.
	 */
	public function __construct(array $queries = [])
	{
		foreach ($queries as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Maps `$key` to `$className`, overwriting any existing mapping. Throws
	 * if `$className` is not a subclass of `Query`.
	 *
	 * @param class-string<Query> $className
	 */
	public function register(string $key, string $className): void
	{
		if (! is_subclass_of($className, Query::class)) {
			throw InvalidTypeException::notSubclassOf(esc_html($className), Query::class);
		}

		$this->queries[$key] = $className;
	}

	/**
	 * Removes the mapping for `$key`, if any.
	 */
	public function unregister(string $key): void
	{
		unset($this->queries[$key]);
	}

	/**
	 * Returns whether a query class is registered under `$key`.
	 */
	public function isRegistered(string $key): bool
	{
		return array_key_exists($key, $this->queries);
	}

	/**
	 * Returns the query class registered under `$key`, or `null` if none.
	 *
	 * @return null|class-string<Query>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->queries[$key] : null;
	}
}
