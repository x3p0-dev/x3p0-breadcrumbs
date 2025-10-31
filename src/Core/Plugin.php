<?php

namespace X3P0\Breadcrumbs\Core;

use X3P0\Breadcrumbs\Block\BlockServiceProvider;
use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Rest\RestServiceProvider;

/**
 * The Plugin class is an implementation of the Application interface. It's used
 * to bootstrap the plugin and register the default service providers.
 */
final class Plugin implements Application
{
	/**
	 * Stores an array of the registered service providers.
	 */
	private array $providers = [];

	/**
	 * Registers the default service providers.
	 */
	public function __construct(protected Container $container)
	{
		$this->register(BlockServiceProvider::class);
		$this->register(RestServiceProvider::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function container(): Container
	{
		return $this->container;
	}

	/**
	 * {@inheritDoc}
	 */
	public function register(string|object $provider): void
	{
		if (! is_subclass_of($provider, ServiceProvider::class)) {
			return;
		}

		if (is_string($provider)) {
			$provider = new $provider($this->container);
		}

		$provider->register();
		$this->providers[] = $provider;
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot(): void
	{
		foreach ($this->providers as $provider) {
			if ($provider instanceof Bootable) {
				$provider->boot();
			}
		}
	}
}
