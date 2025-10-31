<?php

namespace X3P0\Breadcrumbs\Core;

interface Application
{
	/**
	 * Get the container instance.
	 */
	public function container(): Container;

	/**
	 * Register a service provider with the application.
	 */
	public function register(string|ServiceProvider $provider): void;

	/**
	 * Boot all registered service providers.
	 */
	public function boot(): void;
}
