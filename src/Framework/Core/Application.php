<?php

/**
 * Abstract application class.
 *
 * @version   1.0.0
 * @package   X3P0\Framework
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Framework\Core;

use InvalidArgumentException;
use X3P0\Breadcrumbs\Framework\Contracts\Bootable;

/**
 * Base class that does the heavy lifting of bootstrapping an application while
 * letting subclasses handle the registration aspects specific to them.
 */
abstract class Application
{
	/**
	 * Custom namespace/prefix for WordPress hooks that can be defined in a
	 * subclass. If left undefined, hooks will not fire.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const NAMESPACE = '';

	/**
	 * An array of service provider classnames to automatically register if
	 * defined in a subclass.
	 *
	 * @var  array<string> Service provider classnames.
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const PROVIDERS = [];

	/**
	 * Stores an array of the registered service providers.
	 */
	private array $serviceProviders = [];

	/**
	 * Sets up the initial object state.
	 */
	public function __construct(protected readonly Container $container)
	{
		// Register default bindings and service providers.
		$this->registerDefaultBindings();
		$this->registerDefaultProviders();

		// Allow third-party devs to register service providers.
		if (static::NAMESPACE !== '') {
			do_action(static::NAMESPACE . '/register', $this);
		}
	}

	/**
	 * Registers default container bindings.
	 */
	protected function registerDefaultBindings(): void
	{
		$this->container->instance(Container::class, $this->container);
	}

	/**
	 * Registers the default service providers.
	 */
	protected function registerDefaultProviders(): void
	{
		foreach (static::PROVIDERS as $provider) {
			$this->register($provider);
		}
	}

	/**
	 * Get the container instance.
	 */
	public function container(): Container
	{
		return $this->container;
	}

	/**
	 * Register a service provider with the application.
	 */
	public function register(ServiceProvider|string $provider): void
	{
		if (is_string($provider)) {
			if (! is_subclass_of($provider, ServiceProvider::class)) {
				throw new InvalidArgumentException(sprintf(
					// Translators: %s is a classname.
					__('Provider must be a %s class', 'x3p0-breadcrumbs'),
					ServiceProvider::class
				));
			}

			$provider = new $provider($this->container);
		}

		$provider->register();
		$this->serviceProviders[] = $provider;
	}

	/**
	 * Boots all service providers that implement the `Bootable` interface.
	 */
	public function boot(): void
	{
		foreach ($this->serviceProviders as $provider) {
			if ($provider instanceof Bootable) {
				$provider->boot();
			}
		}

		// Allow third-party devs access to hook in after booting.
		if (static::NAMESPACE !== '') {
			do_action(static::NAMESPACE . '/booted', $this);
		}
	}
}
