<?php

/**
 * Crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * `Crumb` classes represent the final result of an individual breadcrumb item
 * that has been generated either by `Query` or `Assembler` implementations. It
 * should house all the information for outputting the breadcrumb item on the
 * front end.
 */
abstract class Crumb
{
	/**
	 * Creates a new crumb object.
	 */
	public function __construct(protected BreadcrumbsContext $context)
	{}

	/**
	 * Returns an internationalized text label for the crumb.
	 */
	abstract public function getLabel(): string;

	/**
	 * Returns a URL for the crumb.
	 */
	public function getUrl(): string
	{
		return '';
	}
}
