<?php

/**
 * Crumb factory interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Factory contract for crumb objects.
 */
interface CrumbFactory
{
	/**
	 * Creates an instance of a crumb object.
	 */
	public function make(string $name, array $params = []): ?Crumb;
}
