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
 * edits by using the same public seams available to third parties: it defines
 * its own query, assembler, or crumb types and routes to them by subscribing
 * listeners to the plugin's events (e.g. resolving the query type, relabeling
 * a built crumb) via the `Subscriber` contract. The `ExtensionServiceProvider`
 * only wires up an extension when `isActive()` reports that its target
 * platform is present, so nothing runs when the platform is inactive.
 * Concrete extensions are `final` and live under their own sub-namespace;
 * this class is the typehint the service provider checks.
 */
abstract class Extension implements Subscriber
{
	/**
	 * The container tag under which extensions are collected. Tagging a
	 * concrete with this (during the `x3p0/breadcrumbs/register` action)
	 * opts it into the same boot lifecycle as the built-in extensions.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	final public const TAG = 'x3p0/breadcrumbs/extension';

	/**
	 * Whether the extension should participate in the current request. The
	 * service provider skips an extension entirely when this returns false,
	 * so guard on the target platform's own API (e.g. a class or function
	 * it defines) rather than assuming it is loaded.
	 */
	abstract public function isActive(): bool;

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array
	{
		return [];
	}
}
