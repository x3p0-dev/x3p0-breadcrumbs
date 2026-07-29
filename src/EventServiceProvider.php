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
use X3P0\Breadcrumbs\Packages\Event\Listener\Listenable;
use X3P0\Breadcrumbs\Packages\Event\Listener\ListenerProvider;
use X3P0\Breadcrumbs\Packages\Event\Listener\ListenerRegistry;
use X3P0\Breadcrumbs\Packages\Event\Listener\Subscribable;
use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the framework-agnostic event library into the plugin's container. Two
 * shared instances back the subsystem: an `EventDispatcher` (bound as the
 * overridable `Dispatcher` default) and one `ListenerRegistry` that plays all
 * three of its roles at once — the read-side `ListenerProvider`, the base
 * `Listenable` write contract, and the `Subscribable` layer on top. Because
 * every contract resolves to the same shared instance, the dispatcher reads
 * exactly the listeners that were registered.
 *
 * `ListenerRegistry` is named only in the `register()` factory, which exists
 * because it takes an optional `?Closure` resolver the container cannot
 * autowire; the factory supplies a container-backed resolver so listeners
 * registered by class name are built through the container. `singletonIf`
 * throughout lets an extension swap any binding by binding its own first.
 */
final class EventServiceProvider extends ServiceProvider
{
	/**
	 * Binds the dispatcher and every listener-side contract as overridable
	 * defaults, so an extension may swap any of them by binding it first.
	 * `ListenerProvider`, `Listenable`, and `Subscribable` all delegate to
	 * the one `ListenerRegistry` instance bound in `register()`.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		Dispatcher::class       => EventDispatcher::class,
		ListenerProvider::class => ListenerRegistry::class,
		Listenable::class       => ListenerRegistry::class,
		Subscribable::class     => ListenerRegistry::class
	];

	/**
	 * Registers the event bindings. Runs the parent to apply the
	 * `SINGLETONS_IF` map, then binds the registry itself with a factory,
	 * since a concrete class taking a constructor argument can't live in
	 * `SINGLETONS_IF` as a class-name entry: `ListenerRegistry` takes an
	 * optional `?Closure` resolver the container cannot autowire.
	 * `singletonIf` still lets an extension replace it by binding
	 * `ListenerRegistry` beforehand.
	 */
	public function register(): void
	{
		// The one registry instance listeners and subscribers register
		// on, resolved by `ListenerProvider`, `Listenable`, and
		// `Subscribable` alike. The factory gives it a container-backed
		// resolver so a listener registered by class name is built
		// through the container.
		$this->container->singletonIf(
			ListenerRegistry::class,
			static fn (InstanceResolver $resolver) => new ListenerRegistry(
				static fn (string $class): object => $resolver->make($class)
			)
		);
	}
}
