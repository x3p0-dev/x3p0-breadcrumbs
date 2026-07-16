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
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\Tagged;
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
final class ExtensionServiceProvider extends ServiceProvider
{
	/**
	 * The built-in extensions. Each is bound as an overridable singleton
	 * and tagged, so third parties may replace one and their own tagged
	 * extensions boot alongside these.
	 *
	 * @todo Type hint with PHP 8.3+ requirement.
	 * @var  list<string>
	 */
	protected const EXTENSIONS = [
		WooCommerce::class
	];

	/**
	 * Binds and tags the built-in extensions. Runs the parent first, then
	 * walks `EXTENSIONS` once to register each as an overridable singleton
	 * and tag it under `Extension::TAG`. Driving both from a single list
	 * keeps the binding and the tag in sync: the `singletonIf` binding lets
	 * an extension swap a built-in by binding its own concrete first, and
	 * the shared tag collects those swaps alongside any third-party
	 * extensions for `bootExtensions()`.
	 */
	public function register(): void
	{
		parent::register();

		foreach (self::EXTENSIONS as $extension) {
			$this->container->singletonIf($extension);
		}

		$this->container->tag(self::EXTENSIONS, Extension::TAG);
	}

	/**
	 * Boots the tagged extensions by handing `bootExtensions()` to the
	 * container's `call()`, which resolves that method's dependencies — the
	 * listener registry and the tagged extensions — before invoking it.
	 */
	public function boot(): void
	{
		parent::boot();
		$this->container->call($this->bootExtensions(...));
	}

	/**
	 * Boots each supported extension, letting it register its own query,
	 * assembler, and crumb types and subscribing its event listeners. The
	 * container injects the shared `ListenerRegistry` and, through the
	 * `#[Tagged]` attribute, every service tagged with `Extension::TAG` as
	 * the variadic `$extensions`, whose `Extension` type enforces that each
	 * tagged service is an extension. An extension whose platform is
	 * inactive (`isSupported()` is false) is skipped.
	 */
	private function bootExtensions(
		ListenerRegistry $listeners,
		#[Tagged(Extension::TAG)] Extension ...$extensions
	): void {
		foreach ($extensions as $extension) {
			if ($extension->isSupported()) {
				$extension->register();
				$listeners->subscribe($extension);
			}
		}
	}
}
