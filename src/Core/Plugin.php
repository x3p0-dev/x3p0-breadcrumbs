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
		$this->container->instance(Container::class, $container);

		$this->register(QueryServiceProvider::class);
		$this->register(AssemblerServiceProvider::class);
		$this->register(CrumbServiceProvider::class);
		$this->register(MarkupServiceProvider::class);
		$this->register(BreadcrumbsServiceProvider::class);

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
