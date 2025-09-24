<?php

/**
 * Assembler interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * `Assembler` classes are essentially helper classes for generating breadcrumbs,
 * sitting as a layer between `Query` and `Crumb` classes. They are primarily
 * used for adding crumbs to the overall breadcrumbs collection.
 */
interface Assembler
{
	/**
	 * Runs the logic for generating breadcrumbs.
	 */
	public function assemble(): void;
}
