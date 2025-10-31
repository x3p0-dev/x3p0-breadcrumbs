<?php

namespace X3P0\Breadcrumbs\Core;

/**
 * Service providers allow you to connect services to the application container.
 * This base class should be extended with at least the `register()` method for
 * registering services.
 */
abstract class ServiceProvider
{
	/**
	 * Accepts a container implementation for registering services.
	 */
	public function __construct(protected Container $container)
	{}

	/**
	 * Registers one or more services with the container.
	 */
	abstract public function register(): void;
}
