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
 * Wires the event library into the plugin's container. Two concrete singletons
 * back the whole subsystem: one `EventDispatcher` that fires events and one
 * `ContainerListenerRegistry` that holds the listeners. Everything else is a
 * view onto those two instances, so every part of the plugin shares one
 * dispatcher and one registry of listeners.
 *
 * The dispatcher is bound under `Dispatcher` (fire) and the registry under
 * `ListenerProvider` (read), both with `singletonIf` so an extension may swap
 * either by binding its own concrete. The dispatcher's `ListenerProvider`
 * dependency is autowired to that same shared registry, so listeners registered
 * on it are the ones dispatch reads.
 *
 * The registry is `BreadcrumbsListenerRegistry` rather than the library's
 * `PriorityListenerRegistry` so class-name listeners resolve through the
 * container. It takes only the container in its constructor, so — unlike the
 * library class, whose `?Closure` resolver cannot be autowired — it drops
 * straight into a class-name binding with no factory closure needed.
 *
 * `ListenerRegistry` (write) is exposed as an alias, not a second binding: the
 * event classes declare no `#[Singleton]`, so binding it to the same concrete
 * would build a separate instance; the alias resolves to the already-shared one,
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
		ListenerRegistry::class => ListenerProvider::class
	];

	/**
	 * @inheritDoc
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
