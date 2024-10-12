<?php

/**
 * Query interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * `Query` classes are meant to be paired with the global WordPress queried URL,
 * such as the front page, single posts, archives, etc. Their purpose is to
 * call either `Builder` or `Crumb` classes to generate breadcrumbs.
 */
interface Query
{
	/**
	 * Runs the logic for generating breadcrumbs.
	 */
	public function make(): void;
}
