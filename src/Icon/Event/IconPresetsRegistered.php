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

use X3P0\Breadcrumbs\Icon\IconPresets;
use X3P0\Breadcrumbs\Packages\Event\BroadcastableEvent;
use X3P0\Breadcrumbs\Packages\Event\BroadcastsToHooks;
use X3P0\Breadcrumbs\Packages\Event\Named;
use X3P0\Breadcrumbs\Packages\Event\NamedEvent;

/**
 * Dispatched very late on `init`, once every post type and taxonomy is
 * registered. This is the supported point for third-party code to say what
 * icons its crumbs render and what the block editor offers for them.
 *
 * A listener registers an {@see IconPreset} per key it has something to say
 * about — the icon the key shows, and, when the key is worth setting from
 * the editor, the label and group of its control. One registration covers
 * both. Listeners receive the shared registry, so what they register is what
 * the trail resolves against and what the editor lists.
 */
final class IconPresetsRegistered implements BroadcastableEvent, NamedEvent
{
	use BroadcastsToHooks;
	use Named;

	/**
	 * The name of the WordPress hook this event is bridged to after it is
	 * dispatched, so `add_action()` callbacks can register icons without
	 * resolving anything from the container.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const NAME = 'x3p0/breadcrumbs/icon-presets-registered';

	/**
	 * Stores the registry listeners say their piece to.
	 */
	public function __construct(public readonly IconPresets $presets)
	{}
}
