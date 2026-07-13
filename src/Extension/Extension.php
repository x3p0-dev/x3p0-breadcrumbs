<?php

/**
 * Abstract extension.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension;

use X3P0\Breadcrumbs\Packages\Event\Subscriber;

/**
 * Base contract for a built-in integration with a third-party platform (such as
 * WooCommerce). An extension wires itself into the plugin without any core
 * edits by using the same public seams available to third parties: it registers
 * custom query, assembler, or crumb types in `register()` and subscribes
 * listeners to the plugin's events via the `Subscriber` contract. The
 * `ExtensionServiceProvider` only wires up an extension when `isSupported()`
 * reports that its target platform is present, so nothing runs when the
 * platform is inactive. Concrete extensions are `final` and live under their
 * own sub-namespace; this class is the typehint the service provider checks.
 */
abstract class Extension implements Subscriber
{
	/**
	 * Whether the target platform is present for the current request. The
	 * service provider skips an extension entirely when this returns false,
	 * so guard on the platform's own API (e.g. a class or function it
	 * defines) rather than assuming it is loaded.
	 */
	abstract public function isSupported(): bool;

	/**
	 * Registers the extension's custom query, assembler, and crumb types in
	 * their registries. Called once for supported extensions, before its
	 * listeners are subscribed. Registering an existing key overrides the
	 * built-in type of that key.
	 */
	public function register(): void
	{}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array
	{
		return [];
	}
}
