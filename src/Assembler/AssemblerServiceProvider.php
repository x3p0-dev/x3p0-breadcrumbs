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

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the assembler subsystem into the DI container. The factory and registry
 * are bound as shared singletons, and the registrar is booted so the built-in
 * assembler types are registered on plugin startup.
 */
final class AssemblerServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * The assembler factory and registry, bound as shared services only if
	 * not already bound so extensions may replace them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		AssemblerFactory::class,
		AssemblerRegistry::class
	];

	/**
	 * The registrar booted on startup to seed the built-in assembler types.
	 *
	 * @var  array<string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		AssemblerRegistrar::class
	];
}
