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
use X3P0\Breadcrumbs\Packages\Event\Listener\ListenerProvider;
use X3P0\Breadcrumbs\Packages\Event\Listener\ListenerRegistry;
use X3P0\Breadcrumbs\Packages\Event\Listener\Registry\PriorityRegistry;
use X3P0\Breadcrumbs\Packages\Framework\Container\Container;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the event library into the plugin's container. Two shared singletons
 * back the whole subsystem: an `EventDispatcher` that fires events and a
 * `PriorityRegistry` that holds the listeners. Every other event binding is a
 * view onto those two instances, so the plugin shares one dispatcher and one
 * registry of listeners.
 *
 * The dispatcher is bound under `Dispatcher` (fire) and the registry under
 * `ListenerProvider` (read), both with `singletonIf` so an extension may swap
 * either by binding its own concrete first. The dispatcher's `ListenerProvider`
 * dependency is autowired to that same shared registry, so the listeners it
 * reads at dispatch time are exactly the ones registered on the registry.
 *
 * The registry is bound with a factory (see `register()`) rather than a plain
 * class-name mapping: `PriorityRegistry` takes an optional `?Closure` resolver
 * that the container cannot autowire. The factory supplies a resolver backed by
 * the container, so a listener registered by its class name is built through the
 * container — receiving its own dependencies — instead of the library's
 * `new $class()` fallback.
 *
 * `ListenerRegistry` (the write contract) is exposed as an alias of
 * `ListenerProvider`, not as a second binding: the event library declares no
 * `#[Singleton]`, so binding the same concrete twice would build two separate
 * registries. The alias resolves to the one shared instance, letting code that
 * registers listeners typehint the write contract instead of the read-only
 * `ListenerProvider`.
 *
 * This is the plugin's integration glue for the framework-agnostic event
 * library; the library itself ships no service provider.
 */
final class EventServiceProvider extends ServiceProvider
{
	protected const SINGLETONS_IF = [
		Dispatcher::class => EventDispatcher::class
	];

	protected const ALIASES = [
		ListenerRegistry::class => ListenerProvider::class
	];

	/**
	 * Registers the event bindings. Runs the parent to apply the
	 * `SINGLETONS_IF` and `ALIASES` maps, then binds the listener registry
	 * with a factory that gives `PriorityRegistry` a container-backed
	 * resolver. This binding cannot live in `SINGLETONS_IF` as a class-name
	 * entry because the registry's optional `?Closure` resolver is not
	 * auto-wirable; `singletonIf` still lets an extension replace it by
	 * binding `ListenerProvider` beforehand.
	 */
	public function register(): void
	{
		parent::register();

		$this->container->singletonIf(
			ListenerProvider::class,
			static fn (Container $container) => new PriorityRegistry(
				static fn (string $class): object => $container->get($class)
			)
		);
	}
}
