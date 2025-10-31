<?php

/**
 * Breadcrumbs service class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Markup\MarkupConfig;
use X3P0\Breadcrumbs\Markup\MarkupFactory;

/**
 * Support class for more quickly rendering a breadcrumb trail. It hides away
 * some of the complexity in favor of a simpler API.
 */
class BreadcrumbsService
{
	/**
	 * Sets up the initial service state.
	 */
	public function __construct(
		protected BreadcrumbsFactory $breadcrumbsFactory,
		protected MarkupFactory $markupFactory
	) {}

	/**
	 * Renders the breadcrumbs with by passing in a breadcrumbs config,
	 * markup config, and markup type.
	 *
	 * @todo With minimum PHP 8.1 support, initialize new configs in the constructor.
	 */
	public function render(
		BreadcrumbsConfig|array $breadcrumbsConfig = [],
		MarkupConfig|array      $markupConfig      = [],
		string                  $markupType        = 'html'
	): string {
		$breadcrumbsConfig = is_array($breadcrumbsConfig)
			? BreadcrumbsConfig::fromArray($breadcrumbsConfig)
			: $breadcrumbsConfig;

		$markupConfig = is_array($markupConfig)
			? MarkupConfig::fromArray($markupConfig)
			: $markupConfig;

		$breadcrumbs = $this->breadcrumbsFactory->make([
			'config' => $breadcrumbsConfig
		]);

		$markup = $this->markupFactory->make($markupType, [
			'crumbs' => $breadcrumbs->generate(),
			'config' => $markupConfig
		]);

		return $markup->render();
	}
}
