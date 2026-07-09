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

use X3P0\Breadcrumbs\Markup\MarkupConfig;
use X3P0\Breadcrumbs\Markup\MarkupFactory;
use X3P0\Breadcrumbs\Markup\MarkupType;

/**
 * The public, outside-in entry point for turning a request into a rendered
 * breadcrumb trail. Given a config, it builds the trail (via the breadcrumbs
 * factory) and renders it to a string in the requested markup format (via the
 * markup factory), hiding that two-step pipeline behind a single `render()`
 * call.
 *
 * This is the mirror image of `BreadcrumbsContext`: where the context is the
 * inside-out facade the build participants talk through, this is the outside-in
 * facade callers reach for. It is markup-agnostic — the output format is chosen
 * per call via the `MarkupType` argument.
 *
 * Not declared `final` so the deprecated `BreadcrumbsService` can extend it for
 * backward compatibility; treat it as effectively final otherwise.
 *
 * @see BreadcrumbsService Deprecated alias retained for backward compatibility.
 */
class BreadcrumbsRenderer
{
	/**
	 * Sets up the initial renderer state with the breadcrumbs builder and
	 * the markup factory used to build and render a breadcrumb trail.
	 */
	public function __construct(
		private readonly Breadcrumbs   $breadcrumbs,
		private readonly MarkupFactory $markupFactory
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
		MarkupType|string       $markupType        = MarkupType::Html
	): string {
		$breadcrumbsConfig = is_array($breadcrumbsConfig)
			? BreadcrumbsConfig::fromArray($breadcrumbsConfig)
			: $breadcrumbsConfig;

		$markupConfig = is_array($markupConfig)
			? MarkupConfig::fromArray($markupConfig)
			: $markupConfig;

		$markup = $this->markupFactory->make($markupType, [
			'crumbs' => $this->breadcrumbs->generate($breadcrumbsConfig),
			'config' => $markupConfig
		]);

		return $markup?->render() ?? '';
	}
}
