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

use X3P0\Breadcrumbs\Packages\ClassRegistry\Registry;

/**
 * Stores the `string key => Query class name` mappings that the factory resolves
 * against. Registration rejects any class that is not a `Query` subclass, so
 * every stored value is guaranteed instantiable as a query.
 *
 * @extends Registry<Query>
 */
final class QueryRegistry extends Registry
{
	/**
	 * The base type every registered query must be a subclass of.
	 *
	 * @var  class-string<Query>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const CONTRACT = Query::class;
}
