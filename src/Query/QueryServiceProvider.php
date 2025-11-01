<?php

/**
 * Query service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\ServiceProvider;

class QueryServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->container->singleton(QueryRegistry::class);
		$this->container->singleton(QueryFactory::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		QueryRegistrar::register($this->container->get(QueryRegistry::class));
	}
}
