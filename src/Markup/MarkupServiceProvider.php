<?php

/**
 * Markup service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\ServiceProvider;

final class MarkupServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		$this->container->singleton(MarkupFactory::class);
		$this->container->singleton(MarkupRegistry::class);
	}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		MarkupRegistrar::register($this->container->get(MarkupRegistry::class));
	}
}
