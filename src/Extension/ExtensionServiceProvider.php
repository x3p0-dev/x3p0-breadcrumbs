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

use X3P0\Breadcrumbs\Packages\Event\Listener\Subscribable;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\Tagged;
use X3P0\Breadcrumbs\Packages\Framework\Container\ContainerException;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the plugin's built-in platform extensions into the container and boots
 * the ones whose platform is active. Each extension is bound as an overridable
 * singleton and, on boot, is registered and subscribed only when its
 * `isActive()` check passes, so an inactive platform costs a single guard and
 * nothing more.
 */
final class ExtensionServiceProvider extends ServiceProvider
{
	/**
	 * Binds and tags the built-in extensions. Types `Extension::TAG` so
	 * every member, built-in or third-party, is validated as a concrete
	 * `Extension` when tagged rather than at resolution. Then walks
	 * `ExtensionType::active()` to register each as an overridable singleton
	 * and tag it. Driving both from a single list keeps the binding and the
	 * tag in sync: the `singletonIf` binding lets an extension swap a built-in
	 * by binding its own concrete first, and the shared tag collects those
	 * swaps alongside any third-party extensions for `bootExtensions()`.
	 *
	 * @throws ContainerException
	 */
	public function register(): void
	{
		$this->container->setTagContract(Extension::TAG, Extension::class);

		foreach (ExtensionType::active() as $extension) {
			$this->container->singletonIf($extension);
			$this->container->tag($extension, Extension::TAG);
		}
	}

	/**
	 * Boots the tagged extensions by handing `bootExtensions()` to the
	 * container's `call()`, which resolves that method's dependencies — the
	 * listener registry and the tagged extensions — before invoking it.
	 */
	public function boot(): void
	{
		$this->container->call($this->bootExtensions(...));
	}

	/**
	 * Boots each active extension and subscribes it via the listener registry.
	 */
	private function bootExtensions(
		Subscribable $registry,
		#[Tagged(Extension::TAG)] Extension ...$extensions
	): void {
		foreach ($extensions as $extension) {
			$extension->boot();
			$registry->subscribe($extension);
		}
	}
}
