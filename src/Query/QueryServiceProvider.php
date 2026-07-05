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

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the query subsystem into the container: binds the registry, factory,
 * and resolver as shared singletons (only if not already bound) and boots the
 * registrar that seeds the built-in query types.
 */
final class QueryServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * The query factory, registry, and resolver, bound as shared singletons
	 * only if not already bound so extensions may replace them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		QueryFactory::class,
		QueryRegistry::class,
		QueryResolver::class
	];

	/**
	 * The registrar booted on startup to seed the built-in query types.
	 *
	 * @var  array<string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		QueryRegistrar::class
	];
}
