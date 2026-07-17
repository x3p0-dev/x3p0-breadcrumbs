<?php

/**
 * Breadcrumbs renderer class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Markup\Event\MarkupRendering;
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use X3P0\Breadcrumbs\Markup\MarkupFactory;
use X3P0\Breadcrumbs\Markup\MarkupKey;
use X3P0\Breadcrumbs\Markup\MarkupType;
use X3P0\Breadcrumbs\Packages\Event\Dispatcher;

/**
 * The public, outside-in entry point for turning a request into a rendered
 * breadcrumb trail. Given a config, it builds the trail (via the breadcrumbs
 * generator) and renders it to a string in the requested markup format (via the
 * markup factory), hiding that two-step pipeline behind a single `render()`
 * call. Between the two steps it dispatches the `MarkupRendering` event, so
 * listeners can retarget the markup type or config for the current request.
 *
 * This is the mirror image of `BreadcrumbsContext`: where the context is the
 * inside-out facade the build participants talk through, this is the outside-in
 * facade callers reach for. It is markup-agnostic — the output format is chosen
 * per call via the `MarkupKey` argument (typically a `MarkupType` case).
 *
 * Not declared `final` so the deprecated `BreadcrumbsService` can extend it for
 * backward compatibility; treat it as effectively final otherwise.
 *
 * @see BreadcrumbsService Deprecated alias retained for backward compatibility.
 */
class BreadcrumbsRenderer
{
	/**
	 * Sets up the initial renderer state with the breadcrumbs generator and
	 * the markup factory used to build and render a breadcrumb trail, plus
	 * the dispatcher that lets listeners retarget rendering.
	 */
	public function __construct(
		private readonly BreadcrumbsGenerator $generator,
		private readonly MarkupFactory        $markupFactory,
		private readonly Dispatcher           $events
	) {}

	/**
	 * Builds and renders a breadcrumb trail, returning the markup as a string.
	 *
	 * Each argument accepts either a typed object or the loose value it is
	 * built from: the configs may be passed as arrays (coerced via their
	 * `fromArray()` factories), and the markup type may be passed as a string
	 * key. Returns an empty string if the markup type is not registered.
	 */
	public function render(
		BreadcrumbsConfig|array $breadcrumbsConfig = new BreadcrumbsConfig(),
		MarkupConfig|array      $markupConfig      = new MarkupConfig(),
		MarkupKey|string        $markupType        = MarkupType::Html
	): string {
		$breadcrumbsConfig = is_array($breadcrumbsConfig)
			? BreadcrumbsConfig::fromArray($breadcrumbsConfig)
			: $breadcrumbsConfig;

		$markupConfig = is_array($markupConfig)
			? MarkupConfig::fromArray($markupConfig)
			: $markupConfig;

		$crumbs = $this->generator->generate($breadcrumbsConfig);

		// Let listeners retarget the markup type or config for this
		// request, then bridge the same event to WordPress unless a
		// listener claimed stopped propagation, so `add_action()`
		// callbacks can retarget it alongside the typed listeners.
		$event = $this->events->dispatch(new MarkupRendering(
			crumbs:     $crumbs,
			markupType: $markupType,
			config:     $markupConfig
		));

		if (! $event->isPropagationStopped()) {
			do_action(MarkupRendering::HOOK_NAME, $event);
		}

		$markup = $this->markupFactory->make($event->getMarkupType(), [
			'crumbs' => $crumbs,
			'config' => $event->getConfig()
		]);

		return $markup?->render() ?? '';
	}
}
