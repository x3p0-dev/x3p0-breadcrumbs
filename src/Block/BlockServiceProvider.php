<?php

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\ServiceProvider;

class BlockServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->container->singleton(BlockRegistrar::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		$this->container->get(BlockRegistrar::class, [
			'path' => __DIR__ . '/../../public/blocks'
		])->boot();
	}
}
