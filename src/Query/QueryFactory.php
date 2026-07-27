<?php

/**
 * Query factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Builds a `Query` instance from a type identifier. Returns `null` when
 * resolution is not successful, so callers can dispatch optimistically.
 */
final class QueryFactory
{
	/**
	 * Stores the resolver that builds the mapped class through the container.
	 */
	public function __construct(private readonly InstanceResolver $resolver)
	{}

	/**
	 * Builds the query for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 *
	 * Construct the type via a class-string or an enum that implements the
	 * {@see QueryDefinition} interface (the class can be derived from
	 * the enum).
	 */
	public function make(QueryDefinition|string $type, array $params = []): ?Query
	{
		$type = is_string($type) ? $type : $type->className();

		if (is_subclass_of($type, Query::class)) {
			/** @var Query */
			return $this->resolver->make($type, $params);
		}

		return null;
	}
}
