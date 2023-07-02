<?php
/**
 * Crumb interface.
 *
 * Defines the interface that crumb classes must use.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Contracts;

interface Crumb {

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
}
