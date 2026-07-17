<?php

/**
 * Markup rendering event.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Event;

use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use X3P0\Breadcrumbs\Markup\MarkupKey;
use X3P0\Breadcrumbs\Packages\Event\Stoppable;
use X3P0\Breadcrumbs\Packages\Event\StoppableEvent;

/**
 * Dispatched after the trail is built but before it is rendered, so listeners
 * can change how a finished trail is presented for the current request. Carries
 * the finished crumbs read-only — mutate them on the `CrumbsBuilt` event
 * instead — along with the markup type and markup config to render with, both
 * mutable: swap the type with `setMarkupType()` to render a different format,
 * or replace the config with `setConfig()` to adjust its options. Pass any
 * `MarkupKey` (such as a `MarkupType` case) for a typed reference or a string
 * key for a custom one. The renderer reads the final type and config back from
 * this same instance.
 */
final class MarkupRendering implements StoppableEvent
{
	use Stoppable;

	/**
	 * The name of the WordPress action this event is bridged to after it is
	 * dispatched, so `add_action()` callbacks can retarget rendering
	 * alongside the typed listeners.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const HOOK_NAME = 'x3p0/breadcrumbs/markup-rendering';

	/**
	 * Stores the finished crumbs and the mutable markup type and config to
	 * render with. The crumbs are the same collection the build produced;
	 * the type accepts any `MarkupKey` (such as a `MarkupType` case) or a
	 * string key for a custom format.
	 */
	public function __construct(
		public readonly CrumbCollection $crumbs,
		private MarkupKey|string $markupType,
		private MarkupConfig $config
	) {}

	/**
	 * Returns the markup type to render with: a `MarkupKey` (such as a
	 * `MarkupType` case) or a string key for a custom format.
	 */
	public function getMarkupType(): MarkupKey|string
	{
		return $this->markupType;
	}

	/**
	 * Overrides the markup type to render with. Pass any `MarkupKey` (such
	 * as a `MarkupType` case) or a string key for a custom one.
	 */
	public function setMarkupType(MarkupKey|string $markupType): void
	{
		$this->markupType = $markupType;
	}

	/**
	 * Returns the markup config to render with.
	 */
	public function getConfig(): MarkupConfig
	{
		return $this->config;
	}

	/**
	 * Overrides the markup config to render with.
	 */
	public function setConfig(MarkupConfig $config): void
	{
		$this->config = $config;
	}
}
