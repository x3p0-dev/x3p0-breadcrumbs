<?php

/**
 * Abstract assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * Base contract for all assemblers. An assembler is a helper that sits between
 * the `Query` and `Crumb` layers: given the current request, it decides which
 * crumbs to add to the trail and which other assemblers to delegate to. All
 * work is dispatched through the shared `BreadcrumbsContext`, which exposes
 * `addCrumb()`, `assemble()`, `config()`, and `crumbs()`. Concrete assemblers
 * are `final` and live under the `Type` sub-namespace; this class is the
 * typehint used throughout the subsystem and is never instantiated directly.
 */
abstract class Assembler
{
	/**
	 * Stores the shared context facade that assemblers use to add crumbs and
	 * delegate to other assemblers.
	 */
	public function __construct(protected BreadcrumbsContext $context)
	{}

	/**
	 * Inspects the current request (and any constructor-injected subject,
	 * such as a post or term) and adds the appropriate crumbs and
	 * sub-assemblers to the trail via the context.
	 */
	abstract public function assemble(): void;
}
