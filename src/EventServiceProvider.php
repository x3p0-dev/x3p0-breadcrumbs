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
use X3P0\Breadcrumbs\Packages\Event\ListenerAwareDispatcher;
use X3P0\Breadcrumbs\Packages\Event\ListenerProvider;
use X3P0\Breadcrumbs\Packages\Event\ListenerRegistry;
use X3P0\Breadcrumbs\Packages\Event\Provider\PriorityListenerRegistry;
use X3P0\Breadcrumbs\Packages\Framework\Container\Container;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the event library into the plugin's container. Two concrete singletons
 * back the whole subsystem: one `EventDispatcher` that fires events and one
 * `PriorityListenerRegistry` that holds the listeners. Everything else is a view
 * onto those two instances, so every part of the plugin shares one dispatcher
 * and one registry of listeners.
 *
 * The dispatcher is bound under `Dispatcher` (fire) and the registry under
 * `ListenerProvider` (read), both with `singletonIf` so an extension may swap
 * either by binding its own concrete. The dispatcher's `ListenerProvider`
 * dependency is autowired to that same shared registry, so listeners registered
 * on it are the ones dispatch reads.
 *
 * The registry cannot be autowired from its class name: its constructor takes a
 * `?Closure $resolver`, and the container has no way to build a `Closure`. So it
 * is bound with a factory that supplies the resolver — a container callback that
 * builds listeners registered by class name through the container, injecting
 * their own dependencies, instead of the library's `new $class()` default.
 *
 * The remaining contracts are exposed as aliases rather than bindings: the event
 * classes declare no `#[Singleton]`, so binding a second contract to the same
 * concrete would build a separate instance. Aliases resolve to the already-shared
 * instance instead. `ListenerAwareDispatcher` (fire + register) is the same
 * `EventDispatcher`; `ListenerRegistry` (write) is the same `PriorityListenerRegistry`,
 * letting registration code typehint the write contract instead of the read-only
 * `ListenerProvider`. The `events` alias resolves to the dispatcher.
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
		'events'                       => Dispatcher::class,
		ListenerAwareDispatcher::class => Dispatcher::class,
		ListenerRegistry::class        => ListenerProvider::class
	];

	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		// Bind the listener registry with a container-backed resolver so
		// listeners registered by class name are built through the container,
		// receiving their own dependencies, rather than with `new $class()`.
		// A plain class-name binding cannot be autowired here because the
		// registry's constructor takes a `?Closure` the container cannot build.
		$this->container->singletonIf(
			ListenerProvider::class,
			static fn (Container $container): PriorityListenerRegistry =>
				new PriorityListenerRegistry(
					static fn (string $listener): object =>
						$container->get($listener)
				)
		);

		parent::register();
	}
}
