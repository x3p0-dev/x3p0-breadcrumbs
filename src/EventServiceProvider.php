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
use X3P0\Breadcrumbs\Packages\Event\Provider\PriorityListenerProvider;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the event library into the plugin's container. The dispatcher and
 * listener provider are each bound interface to concrete, as shared singletons,
 * so every part of the plugin shares one dispatcher and one registry of
 * listeners. The dispatcher's `ListenerProvider` dependency is autowired to that
 * same shared provider. Both use `singletonIf` so an extension may swap in its
 * own implementation, and the `events` alias resolves to the dispatcher.
 *
 * This is the plugin's integration glue for the framework-agnostic event
 * library; the library itself ships no service provider.
 */
final class EventServiceProvider extends ServiceProvider
{
	protected const SINGLETONS_IF = [
		Dispatcher::class       => EventDispatcher::class,
		ListenerProvider::class => PriorityListenerProvider::class
	];

	protected const ALIASES = [
		'events' => Dispatcher::class
	];
}
