<?php

/**
 * The Plugin class is a simple container used to store and reference the various
 * Plugin components. It doesn't support automatic dependency injection (manual
 * only) because it would be overkill for this project.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-ideas
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Block\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Bootable;

class Plugin implements Bootable
{
	/**
	 * Stored definitions of single instances.
	 */
	private array $instances = [];

	/**
	 * Registers the default container bindings.
	 */
	public function __construct()
	{
		$this->registerDefaultBindings();
	}

	/**
	 * Boots the component by calling bootable bindings.
	 */
	#[\Override]
	public function boot(): void
	{
		foreach ($this->instances as $binding) {
			$binding instanceof Bootable && $binding->boot();
		}
	}

	/**
	 * Registers the default bindings we need to run the Plugin.
	 */
	private function registerDefaultBindings(): void
	{
		$this->instance(
			'block.breadcrumbs',
			new Breadcrumbs(untrailingslashit(__DIR__ . '/..'))
		);
	}

	/**
	 * Registers a single instance of a binding.
	 */
	public function instance(string $abstract, mixed $instance): void
	{
		$this->instances[$abstract] = $instance;
	}

	/**
	 * Returns a binding or `null`.
	 */
	public function get(string $abstract): mixed
	{
		return $this->has($abstract) ? $this->instances[$abstract] : null;
	}

	/**
	 * Checks if a binding exists.
	 */
	public function has(string $abstract): bool
	{
		return isset($this->instances[$abstract]);
	}
}
