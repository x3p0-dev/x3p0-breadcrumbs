<?php

/**
 * Crumbs built event.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Event;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbBuilder;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbDefinition;
use X3P0\Breadcrumbs\Packages\Event\BroadcastableEvent;
use X3P0\Breadcrumbs\Packages\Event\BroadcastsToHooks;
use X3P0\Breadcrumbs\Packages\Event\Named;
use X3P0\Breadcrumbs\Packages\Event\NamedEvent;

/**
 * Dispatched after the query has populated the crumbs, before they are
 * returned. Listeners receive the finished, mutable collection — the same
 * instance the build used, so changes made here are what callers ultimately
 * receive — plus `makeCrumb()`/`addCrumb()` to build further crumbs through
 * the factory. There is no `query()` or `assemble()` reachable from here:
 * listeners get exactly the crumb-building capability they use, via the
 * composed `CrumbBuilder`, not the full build context.
 */
final class CrumbsBuilt implements BroadcastableEvent, NamedEvent
{
	use BroadcastsToHooks;
	use Named;

	/**
	 * The name of the WordPress hook this event is bridged to after it
	 * is dispatched, so `add_action()` callbacks can adjust the finished
	 * crumbs alongside the typed listeners.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const NAME = 'x3p0/breadcrumbs/crumbs-built';

	/**
	 * Stores the finished, mutable crumb collection and the builder that
	 * makes/adds further crumbs through the factory.
	 */
	public function __construct(
		public  readonly CrumbCollection $crumbs,
		private readonly CrumbBuilder    $builder
	) {}

	/**
	 * Builds a crumb by type and returns it without adding it to the
	 * collection. Returns null for an unknown type.
	 */
	public function makeCrumb(CrumbDefinition|string $type, array $params = []): ?Crumb
	{
		return $this->builder->makeCrumb($type, $params);
	}

	/**
	 * Builds a crumb by type and appends it to the shared collection. Does
	 * nothing when the type is unknown.
	 */
	public function addCrumb(CrumbDefinition|string $type, array $params = []): void
	{
		$this->builder->addCrumb($type, $params);
	}
}
