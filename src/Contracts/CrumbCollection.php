<?php

/**
 * Iterable collection interface for crumbs.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Interface for a collection of breadcrumbs with iteration capabilities.
 */
interface CrumbCollection extends IterableCollection
{
	/**
	 * Sets a crumb instance in the collection.
	 */
	public function set(?string $name, Crumb $crumb): void;

	/**
	 * Gets a crumb instance from the collection.
	 */
	public function get(string $name): ?Crumb;
}
