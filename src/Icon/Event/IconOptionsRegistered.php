<?php

/**
 * Icon options registering event.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon\Event;

use X3P0\Breadcrumbs\Icon\IconOptionGroupRegistry;
use X3P0\Breadcrumbs\Icon\IconOptionRegistry;
use X3P0\Breadcrumbs\Packages\Event\BroadcastableEvent;
use X3P0\Breadcrumbs\Packages\Event\BroadcastsToHooks;
use X3P0\Breadcrumbs\Packages\Event\Named;
use X3P0\Breadcrumbs\Packages\Event\NamedEvent;

/**
 * Dispatched once the built-in icon options and groups are seeded, late on
 * `init`. This is the supported point for third-party code to register or
 * amend either: every post type and taxonomy is enumerated by now, so a
 * listener sees the finished built-in set. Listeners receive the shared
 * registries themselves, so what they register is what the block editor lists
 * and what the markup layer resolves against.
 */
final class IconOptionsRegistered implements BroadcastableEvent, NamedEvent
{
	use BroadcastsToHooks;
	use Named;

	/**
	 * The name of the WordPress hook this event is bridged to after it is
	 * dispatched, so `add_action()` callbacks can register icon options
	 * without resolving anything from the container.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const NAME = 'x3p0/breadcrumbs/icon-options-registered';

	/**
	 * Stores the seeded registries listeners add to or amend.
	 */
	public function __construct(
		public readonly IconOptionRegistry      $options,
		public readonly IconOptionGroupRegistry $groups
	) {}
}
