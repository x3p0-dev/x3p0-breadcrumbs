<?php

/**
 * Query registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Support\EnumRegistrar;

/**
 * Seeds the registry with the built-in query types on boot. Iterates the
 * `QueryType` enum, mapping each case's string key to its concrete class, and
 * skips any key a third party has already registered so custom overrides win.
 */
final class QueryRegistrar extends EnumRegistrar
{
	/**
	 * The enum whose cases seed the query registry.
	 *
	 * @var  class-string<QueryType>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ENUM = QueryType::class;

	/**
	 * Type-hints the query registry so the container injects it, then hands
	 * it to the base registrar.
	 */
	public function __construct(QueryRegistry $registry)
	{
		parent::__construct($registry);
	}
}
