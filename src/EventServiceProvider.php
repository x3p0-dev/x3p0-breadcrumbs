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
use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the framework-agnostic event library into the plugin's container. Two
 * shared instances back the subsystem: an `EventDispatcher` (bound as the
 * overridable `Dispatcher` default) and one listener registry that both stores
 * listeners (the `ListenerRegistry` write contract) and supplies them at
 * dispatch (the `ListenerProvider` read contract, bound to delegate to the
 * write contract). Because both contracts share the one instance, the dispatcher
 * reads exactly the listeners that were registered.
 *
 * The concrete `PriorityListenerRegistry` is named only in the `register()`
 * factory, which exists because the registry takes an optional `?Closure`
 * resolver the container cannot autowire; the factory supplies a
 * container-backed resolver so listeners registered by class name are built
 * through the container. `singletonIf` throughout lets an extension swap any
 * binding by binding its own first.
 */
final class EventServiceProvider extends ServiceProvider
{
	/**
	 * Binds the dispatcher and the read-side listener provider as overridable
	 * defaults, so an extension may swap either by binding it first. The
	 * `ListenerProvider` read contract delegates to the `ListenerRegistry`
	 * write contract, which the container resolves to the shared registry
	 * bound in `register()`.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
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
		// The one registry instance listeners and subscribers register
		// on, resolved by both the `ListenerRegistry` write contract
		// and the `ListenerProvider` read contract that delegates to it.
		// The factory gives it a container-backed resolver so a listener
		// registered by class name is built through the container.
		$this->container->singletonIf(
			ListenerRegistry::class,
			static fn (InstanceResolver $resolver) => new PriorityListenerRegistry(
				static fn (string $class): object => $resolver->make($class)
			)
		);
	}
}
