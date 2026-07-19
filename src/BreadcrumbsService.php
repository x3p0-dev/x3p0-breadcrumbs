<?php

/**
 * Breadcrumbs service class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Markup\MarkupFactory;
use X3P0\Breadcrumbs\Packages\Event\Dispatcher;

/**
 * Deprecated alias of `BreadcrumbsRenderer`, retained so existing code that
 * type-hints or resolves `BreadcrumbsService` keeps working. New code should
 * use `BreadcrumbsRenderer` directly.
 *
 * @deprecated 5.0.0 Use {@see BreadcrumbsRenderer} instead.
 */
final class BreadcrumbsService extends BreadcrumbsRenderer
{
	/**
	 * Emits a deprecation notice, then forwards to the parent renderer.
	 *
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsGenerator $generator,
		MarkupFactory        $markupFactory,
		Dispatcher           $events
	) {
		_deprecated_class(self::class, '5.0.0', BreadcrumbsRenderer::class);

		parent::__construct($generator, $markupFactory, $events);
	}
}
