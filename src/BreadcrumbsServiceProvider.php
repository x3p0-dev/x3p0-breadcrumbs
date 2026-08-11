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
 * it builds trails with. Both are bound as shared singletons.
 */
final class BreadcrumbsServiceProvider extends ServiceProvider
{
	/**
	 * The generator and renderer, bound as shared singletons.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS = [
		BreadcrumbsGenerator::class,
		BreadcrumbsRenderer::class
	];
}
