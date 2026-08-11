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
 * singleton, and binds each built-in `CrumbType` case's class.
 */
final class CrumbServiceProvider extends ServiceProvider
{
	/**
	 * The crumb factory, bound as a shared singleton.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS = [
		CrumbFactory::class
	];

	/**
	 * Binds each built-in crumb. The enum is the source of truth for the
	 * canonical mapping.
	 */
	public function register(): void
	{
		foreach (CrumbType::cases() as $type) {
			$this->container->transientIf($type->className());
		}
	}
}
