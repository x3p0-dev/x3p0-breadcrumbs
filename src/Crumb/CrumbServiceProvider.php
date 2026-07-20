<?php

/**
 * Crumb service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the crumb subsystem into the container: binds the factory as a shared
 * singleton (only if not already bound) so extensions may replace it, and
 * registers each built-in `CrumbType` value as a container alias for its class.
 * Crumbs are then built by the factory from their key or class name.
 */
final class CrumbServiceProvider extends ServiceProvider
{
	/**
	 * The crumb factory, bound as a shared singleton only if not already bound
	 * so extensions may replace it.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		CrumbFactory::class
	];

	/**
	 * Aliases each built-in crumb key to its class, so callers may dispatch by
	 * the `CrumbType` case, its string key, or the class name. The enum is the
	 * source of truth for the mapping.
	 */
	public function register(): void
	{
	//	foreach (CrumbType::cases() as $type) {
	//		$this->container->alias($type->alias(), $type->className());
	//	}
	}
}
