<?php

/**
 * Crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * Abstract base for a single item in the breadcrumb trail. A crumb is created
 * by a `Query` or `Assembler` and exposes everything needed to output the item:
 * a text label and, optionally, a URL. Concrete crumbs live under `Type` and
 * read shared config and factories from the supplied context. This class is the
 * contract that the rest of the system typehints against; subclasses override
 * `getLabel()` (and `getUrl()` where the crumb links somewhere).
 */
abstract class Crumb
{
	/**
	 * Stores the shared breadcrumbs context, the entry point to config and
	 * the collection being built.
	 */
	public function __construct(protected readonly BreadcrumbsContext $context)
	{}

	/**
	 * Returns the internationalized text label shown for the crumb.
	 */
	abstract public function getLabel(): string;

	/**
	 * Returns the crumb URL, or an empty string when the crumb is not a link.
	 */
	public function getUrl(): string
	{
		return '';
	}
}
