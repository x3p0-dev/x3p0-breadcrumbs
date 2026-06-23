<?php

/**
 * Assembler service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Framework\Core\ServiceProvider;

/**
 * Wires the assembler subsystem into the DI container. The factory and registry
 * are bound as shared singletons, and the registrar is booted so the built-in
 * assembler types are registered on plugin startup.
 */
final class AssemblerServiceProvider extends ServiceProvider implements Bootable
{
	protected const SINGLETONS_IF = [
		AssemblerFactory::class,
		AssemblerRegistry::class
	];

	protected const BOOTABLE = [
		AssemblerRegistrar::class
	];
}
