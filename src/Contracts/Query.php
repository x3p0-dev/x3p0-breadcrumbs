<?php
/**
 * Query interface.
 *
 * Defines the interface that query classes must use.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

interface Query
{
	/**
	 * Builds breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void;
}
