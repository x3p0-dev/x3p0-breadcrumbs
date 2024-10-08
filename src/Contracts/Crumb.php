<?php

/**
 * Crumb interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * `Crumb` classes represent the final result of an individual breadcrumb item
 * that has been generated either by `Query` or `Build` implementations. It
 * should house all the information for outputting the breadcrumb item on the
 * front end.
 */
interface Crumb
{
	/**
	 * Returns a type for the crumb.
	 */
	public function type(): string;

	/**
	 * Returns an internationalized text label for the crumb.
	 */
	public function label(): string;

	/**
	 * Returns a URL for the crumb.
	 */
	public function url(): string;

	/**
	 * Returns whether the crumb should be visually hidden on display.
	 */
	public function visuallyHidden(): bool;
}
