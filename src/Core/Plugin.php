<?php

/**
 * Plugin application class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Core;

use X3P0\Breadcrumbs\Assembler\AssemblerServiceProvider;
use X3P0\Breadcrumbs\Block\BlockServiceProvider;
use X3P0\Breadcrumbs\BreadcrumbsServiceProvider;
use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Crumb\CrumbServiceProvider;
use X3P0\Breadcrumbs\Markup\MarkupServiceProvider;
use X3P0\Breadcrumbs\Query\QueryServiceProvider;
use X3P0\Breadcrumbs\Rest\RestServiceProvider;

/**
 * The Plugin class is an implementation of the Application interface. It's used
 * to register and boot the default service providers, bootstrapping the plugin.
 */
final class Plugin implements Application
{
	/**
	 * Stores an array of the registered service providers.
	 */
	private array $providers = [];

	/**
	 * Sets up the initial object state.
	 */
	public function __construct(protected readonly Container $container)
	{
		// Allow third-party devs to hook in before anything runs.
		do_action('x3p0/breadcrumbs/init', $this);

		// Register default bindings and service providers.
		$this->registerDefaultBindings();
		$this->registerDefaultProviders();

		// Allow third-party devs to register service providers.
		do_action('x3p0/breadcrumbs/register', $this);
	}

	/**
	 * Registers default container bindings.
	 */
	private function registerDefaultBindings(): void
	{
		$this->container->instance(Container::class, $this->container);
	}

	/**
	 * Registers the default service providers.
	 */
	private function registerDefaultProviders(): void
	{
		$this->register(AssemblerServiceProvider::class);
		$this->register(BreadcrumbsServiceProvider::class);
		$this->register(BlockServiceProvider::class);
		$this->register(CrumbServiceProvider::class);
		$this->register(MarkupServiceProvider::class);
		$this->register(QueryServiceProvider::class);
		$this->register(RestServiceProvider::class);
	}

	/**
	 * @inheritDoc
	 */
	public function container(): Container
	{
		return $this->container;
	}

	/**
	 * @inheritDoc
	 */
	public function register(ServiceProvider|string $provider): void
	{
		if (is_string($provider) && is_subclass_of($provider, ServiceProvider::class)) {
			$provider = new $provider($this->container);
		}

		if (! $provider instanceof ServiceProvider) {
			return;
		}

		$provider->register();
		$this->providers[] = $provider;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Boots all service providers that implement the `Bootable` interface.
	 */
	public function boot(): void
	{
		foreach ($this->providers as $provider) {
			if ($provider instanceof Bootable) {
				$provider->boot();
			}
		}

		// Allow third-party devs access to hook in when booting.
		do_action('x3p0/breadcrumbs/boot', $this);
	}
}
