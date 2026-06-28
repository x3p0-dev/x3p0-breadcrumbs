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

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the crumb subsystem into the container: binds the registry and factory
 * as shared singletons and boots the registrar that seeds the built-in types.
 */
final class CrumbServiceProvider extends ServiceProvider implements Bootable
{
	protected const SINGLETONS_IF = [
		CrumbFactory::class,
		CrumbRegistry::class
	];

	protected const BOOTABLE = [
		CrumbRegistrar::class
	];
}
