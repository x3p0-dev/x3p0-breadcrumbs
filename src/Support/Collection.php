<?php

/**
 * Iterable collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

use X3P0\Breadcrumbs\Contracts\IterableCollection;

/**
 * Returns an iterable collection of items.
 */
class Collection implements IterableCollection
{
	/**
	 * Stores the array of items in the collection.
	 */
	protected array $items = [];

	/**
	 * Stores the current index of the iterator.
	 */
	protected int $index = 0;

	/**
	 * {@inheritDoc}
	 */
	public function valid(): bool
	{
		return isset($this->items[$this->index]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function key(): mixed
	{
		return $this->index;
	}

	/**
	 * {@inheritDoc}
	 */
	public function current(): mixed
	{
		return $this->items[$this->index];
	}

	/**
	 * {@inheritDoc}
	 */
	public function next(): void
	{
		$this->index++;
	}

	/**
	 * {@inheritDoc}
	 */
	public function rewind(): void
	{
		$this->index = 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function count(): int
	{
		return count($this->items);
	}

	/**
	 * {@inheritDoc}
	 */
	public function position(): int
	{
		return $this->index + 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isFirst(): bool
	{
		return $this->index === 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isLast(): bool
	{
		return $this->index === $this->count() - 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists(mixed $offset): bool
	{
		return isset($this->items[$offset]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetGet(mixed $offset): mixed
	{
		return $this->items[$offset] ?? null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if ($offset === null) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetUnset(mixed $offset): void
	{
		unset($this->items[$offset]);
	}
}
