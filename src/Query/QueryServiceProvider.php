<?php

/**
 * Query service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the query subsystem into the container: binds the factory and resolver
 * as shared singletons (only if not already bound) so extensions may replace
 * them, and registers each built-in `QueryType` value as a container alias for
 * its class. Queries are then built by the factory from their key or class name.
 */
final class QueryServiceProvider extends ServiceProvider
{
	/**
	 * The query factory and resolver, bound as shared singletons only if not
	 * already bound so extensions may replace them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		QueryFactory::class,
		QueryResolver::class
	];

	/**
	 * Tags each built-in query to the {@see Query::TAG} with its unique
	 * slug, so callers may dispatch by the slug if desired. The enum is the
	 * source of truth for the mapping.
	 */
	public function register(): void
	{
		foreach (QueryType::cases() as $type) {
			$this->container->tag($type->className(), Query::TAG, [
				'slug' => $type->value
			]);
		}
	}
}
