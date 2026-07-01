<?php

/**
 * Container-backed listener registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Event;

use X3P0\Breadcrumbs\Packages\Event\ListenerProvider;
use X3P0\Breadcrumbs\Packages\Event\ListenerRegistry;
use X3P0\Breadcrumbs\Packages\Event\Provider\RegistersListeners;
use X3P0\Breadcrumbs\Packages\Framework\Container\Container;

/**
 * Priority listener registry that resolves class-name listeners through the DI
 * container. It composes the library's `RegistersListeners` behavior — the same
 * implementation the built-in `PriorityListenerRegistry` uses — and differs only
 * in construction: it hands the trait a resolver backed by the container, so a
 * listener registered by class name is built through the container, receiving
 * its own dependencies, rather than with the library's `new $class()` default.
 *
 * This is the plugin-side counterpart the trait's own documentation anticipates,
 * and it stays `final` because the shared behavior arrives by trait, not
 * inheritance.
 */
final class BreadcrumbsListenerRegistry implements ListenerProvider, ListenerRegistry
{
	use RegistersListeners;

	/**
	 * Sets up the registry with a resolver that builds class-name listeners
	 * through the container.
	 */
	public function __construct(Container $container)
	{
		$this->initListenerRegistry(
			static fn (string $class): object => $container->get($class)
		);
	}
}
