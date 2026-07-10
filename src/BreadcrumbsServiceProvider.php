<?php

/**
 * Breadcrumbs service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the top-level breadcrumbs services into the container: the renderer
 * (the public entry point for building and rendering a trail) and the generator
 * it builds trails with, plus the deprecated `BreadcrumbsService` alias. All
 * are bound as shared singletons, and only if not already bound, so extensions
 * may replace them.
 */
final class BreadcrumbsServiceProvider extends ServiceProvider
{
	/**
	 * The generator, renderer, and deprecated service alias, bound as shared
	 * singletons only if not already bound so extensions may replace them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		BreadcrumbsGenerator::class,
		BreadcrumbsRenderer::class,
		// Deprecated alias of `BreadcrumbsRenderer`, kept resolvable for
		// backward compatibility.
		BreadcrumbsService::class
	];
}
