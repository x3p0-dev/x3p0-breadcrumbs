<?php

/**
 * Event service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Packages\Event\Dispatcher;
use X3P0\Breadcrumbs\Packages\Event\EventDispatcher;
use X3P0\Breadcrumbs\Packages\Event\ListenerProvider;
use X3P0\Breadcrumbs\Packages\Event\ListenerRegistry;
use X3P0\Breadcrumbs\Packages\Event\PriorityListenerRegistry;
use X3P0\Breadcrumbs\Packages\Framework\Container\Container;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the event library into the plugin's container. Two shared instances
 * back the subsystem: an `EventDispatcher` that fires events (bound as the
 * overridable `Dispatcher` default) and a single listener registry that both
 * holds the listeners (write) and supplies them at dispatch time (read).
 *
 * Everything is wired against the library's contracts; the concrete
 * `PriorityListenerRegistry` is named in exactly one place — the factory in
 * `register()` that constructs it. That factory (rather than a plain class-name
 * mapping) is needed because the registry takes an optional `?Closure` resolver
 * the container cannot autowire; the factory supplies a resolver backed by the
 * container, so a listener registered by its class name is built through the
 * container — receiving its own dependencies — instead of the library's
 * `new $class()` fallback.
 *
 * The factory is bound under the `ListenerRegistry` write contract (the
 * registry's fuller role — it both stores and provides), and the
 * `ListenerProvider` read contract is bound to delegate to it. Both are shared,
 * so resolving either contract yields the one registry instance: the listeners
 * registered through the write contract are exactly the ones the dispatcher
 * reads through the read contract. `singletonIf` throughout lets an extension
 * swap any binding — the dispatcher, or either contract — by binding its own
 * first; a custom read provider bound ahead of this provider survives, since the
 * read binding is a conditional delegation rather than an unconditional alias.
 *
 * This is the plugin's integration glue for the framework-agnostic event
 * library; the library itself ships no service provider.
 */
final class EventServiceProvider extends ServiceProvider
{
	/**
	 * Binds the dispatcher and the read-side listener provider as overridable
	 * defaults, so an extension may swap either by binding it first. The
	 * `ListenerProvider` read contract delegates to the `ListenerRegistry`
	 * write contract, which the container resolves to the shared registry
	 * bound in `register()`.
	 */
	protected const SINGLETONS_IF = [
		Dispatcher::class       => EventDispatcher::class,
		ListenerProvider::class => ListenerRegistry::class
	];

	/**
	 * Registers the event bindings. Runs the parent to apply the
	 * `SINGLETONS_IF` map, then binds the registry with a factory, since it
	 * cannot live in `SINGLETONS_IF` as a class-name entry:
	 * `PriorityListenerRegistry` takes an optional `?Closure` resolver the
	 * container cannot autowire. `singletonIf` still lets an extension
	 * replace it by binding `ListenerRegistry` beforehand.
	 */
	public function register(): void
	{
		parent::register();

		// The one registry instance listeners and subscribers register
		// on, resolved by both the `ListenerRegistry` write contract
		// and the `ListenerProvider` read contract that delegates to it.
		// The factory gives it a container-backed resolver so a listener
		// registered by class name is built through the container.
		$this->container->singletonIf(
			ListenerRegistry::class,
			static fn (Container $container) => new PriorityListenerRegistry(
				static fn (string $class): object => $container->get($class)
			)
		);
	}
}
