<?php

/**
 * Abstract service provider.
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
	public function __construct(protected readonly Container $container)
	{}

	/**
	 * Registers one or more services with the container.
	 */
	abstract public function register(): void;
}
