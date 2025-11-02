<?php

/**
 * Application interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Core;

use X3P0\Breadcrumbs\Contracts\Bootable;

interface Application extends Bootable
{
	/**
	 * Get the container instance.
	 */
	public function container(): Container;

	/**
	 * Register a service provider with the application.
	 */
	public function register(string|ServiceProvider $provider): void;
}
