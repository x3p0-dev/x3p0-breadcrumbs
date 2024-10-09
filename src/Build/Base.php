<?php

/**
 * Build class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Build;

/**
 * Implements the `Build` interface and creates a custom build object.
 */
abstract class Base implements Build
{
	/**
	 * Creates a new query object.
	 */
	public function __construct(protected Breadcrumbs $breadcrumbs)
	{}
}
