<?php

/**
 * Extension service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension;

use X3P0\Breadcrumbs\Extension\WooCommerce\WooCommerce;
use X3P0\Breadcrumbs\Packages\Event\ListenerRegistry;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the plugin's built-in platform extensions into the container and boots
 * the ones whose platform is active. Each extension is bound as an overridable
 * singleton and, on boot, is registered and subscribed only when its
 * `isSupported()` check passes, so an inactive platform costs a single guard and
 * nothing more. This provider is registered last so extensions boot after the
 * subsystems (query, crumb, event) they build on, letting an extension override
 * a built-in type by re-registering its key.
 */
final class ExtensionServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * The built-in extensions, each resolved and offered a chance to boot.
	 *
	 * @var  array<string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const EXTENSIONS = [
		WooCommerce::class
	];

	/**
	 * The extensions bound as shared singletons only if not already bound,
	 * so third parties may replace one with their own concrete.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		WooCommerce::class
	];

	/**
	 * Boots each supported extension: seeds its custom types into their
	 * registries and subscribes its listeners to the shared listener registry.
	 */
	public function boot(): void
	{
		$listeners = $this->container->get(ListenerRegistry::class);

		foreach (self::EXTENSIONS as $class) {
			$extension = $this->container->get($class);

			if (! $extension->isSupported()) {
				continue;
			}

			$extension->register();
			$listeners->subscribe($extension);
			$extension->boot();
		}
	}
}
