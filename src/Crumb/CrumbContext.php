<?php

/**
 * Crumb context.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Icon\IconConfig;
use X3P0\Breadcrumbs\Icon\IconOptions;

/**
 * Everything a `Crumb` reads its shared state from: the trail config, the
 * caller's icon choices, and the icon options registry. Created once per
 * trail build (by `BreadcrumbsGenerator`) and handed to every crumb through
 * `CrumbBuilder`, so a crumb constructor takes this single facade instead of
 * each collaborator individually — the same role `AssemblerContext` and
 * `QueryContext` play for assemblers and queries.
 */
final class CrumbContext
{
	/**
	 * Stores the shared, read-only trail config, the caller's icon choices,
	 * and the icon options registry.
	 */
	public function __construct(
		public readonly BreadcrumbsConfig $config,
		public readonly IconConfig        $iconConfig,
		public readonly IconOptions       $iconOptions
	) {}
}
