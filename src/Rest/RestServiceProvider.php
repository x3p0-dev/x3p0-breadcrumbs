<?php

namespace X3P0\Breadcrumbs\Rest;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\ServiceProvider;

class RestServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->container->singleton(RestRegistrar::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		$this->container->get(RestRegistrar::class)->boot();
	}
}
