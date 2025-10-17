<?php

/**
 * Iterable collection interface.
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
 * Interface for collections that provide enhanced iteration capabilities.
 */
interface IterableCollection extends ArrayAccess, Iterator, Countable
{
	/**
	 * Checks if the collection is empty.
	 */
	public function isEmpty(): bool;

	/**
	 * Checks if the current iterator position is valid.
	 */
	public function valid(): bool;

	/**
	 * Returns the key of the current element.
	 */
	public function key(): mixed;

	/**
	 * Returns the current element.
	 */
	public function current(): mixed;

	/**
	 * Moves the iterator to the next element.
	 */
	public function next(): void;

	/**
	 * Resets the iterator to the first element.
	 */
	public function rewind(): void;

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
