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

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Interface for a collection of crumb instances with iteration capabilities.
 */
interface CrumbCollection extends ArrayAccess, Iterator, Countable
{
	/**
	 * Returns the current registered crumb type.
	 */
	public function currentType(): string|null;

	/**
	 * Determines whether the current registered crumb is of a specific type.
	 */
	public function currentIsType(string $type): bool;

	/**
	 * Sets a crumb instance in the collection.
	 */
	public function set(string $type, Crumb $crumb): void;

	/**
	 * Gets a crumb instance from the collection.
	 */
	public function get(string $type): ?Crumb;

	/**
	 * Removes a crumb instance from the collection.
	 */
	public function remove(string $type): void;

	/**
	 * Checks if the collection is empty.
	 */
	public function isEmpty(): bool;

	/**
	 * Returns the current position (1-indexed).
	 */
	public function position(): int;

	/**
	 * Checks if the current position is the first element.
	 */
	public function isFirst(): bool;

	/**
	 * Checks if the current position is the last element.
	 */
	public function isLast(): bool;
}
