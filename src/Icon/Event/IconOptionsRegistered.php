<?php

/**
 * Icon options registered event.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon\Event;

use X3P0\Breadcrumbs\Icon\IconOptionRegistry;
use X3P0\Breadcrumbs\Packages\Event\BroadcastableEvent;
use X3P0\Breadcrumbs\Packages\Event\BroadcastsToHooks;
use X3P0\Breadcrumbs\Packages\Event\Named;
use X3P0\Breadcrumbs\Packages\Event\NamedEvent;

/**
 * Dispatched once the built-in icon options are seeded, late on `init`, before
 * anything resolves or lists them. This is the supported point for extensions
 * and third-party code to add their own options or retarget the built-ins:
 * every post type and taxonomy is enumerated by now, so a listener sees the
 * finished set and has the final say over it.
 *
 * Listeners receive the registry itself — the same shared instance every
 * consumer resolves, so changes made here are what the block editor lists and
 * what crumbs resolve against. Use `add()` to register an option outright,
 * `addGroup()` to open a group of one's own for a family of them, and
 * `update()` to change a built-in's icon, label, or group while keeping the
 * parts the registrar derived for it.
 */
final class IconOptionsRegistered implements BroadcastableEvent, NamedEvent
{
	use BroadcastsToHooks;
	use Named;

	/**
	 * The name of the WordPress hook this event is bridged to after it is
	 * dispatched, so `add_action()` callbacks can register icon options
	 * alongside the typed listeners — without resolving anything from the
	 * container.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const NAME = 'x3p0/breadcrumbs/icon-options-registered';

	/**
	 * Stores the seeded registry listeners add to or retarget.
	 */
	public function __construct(public readonly IconOptionRegistry $options)
	{}
}
