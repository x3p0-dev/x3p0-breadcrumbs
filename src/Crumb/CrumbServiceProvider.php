<?php

/**
 * Crumb service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\ServiceProvider;

final class CrumbServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		$this->container->singleton(CrumbRegistry::class);
		$this->container->singleton(CrumbFactory::class);
	}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		CrumbRegistrar::register($this->container->get(CrumbRegistry::class));
	}
}
