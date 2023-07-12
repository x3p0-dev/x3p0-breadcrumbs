<?php
/**
 * Crumb interface.
 *
 * Defines the interface that crumb classes must use.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

interface Crumb
{
	/**
	 * Returns a type for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function type(): string;

	/**
	 * Returns a text label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string;

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function url(): string;

	/**
	 * Returns whether the crumb should be visually hidden on display.
	 *
	 * @since 1.0.0
	 */
	public function visuallyHidden(): bool;
}
