<?php

/**
 * Assembler service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Framework\Core\ServiceProvider;

final class AssemblerServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		$this->container->singleton(AssemblerFactory::class);
		$this->container->singleton(AssemblerRegistry::class);
	}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		AssemblerRegistrar::register($this->container->get(AssemblerRegistry::class));
	}
}
