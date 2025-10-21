<?php

/**
 * Crumb collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Contracts\{Crumb, CrumbCollection};

/**
 * Returns an iterable collection of crumb instances. Supports array access, but
 * it will result in a type error if string keys are not provided. This class is
 * particularly useful when looping through the crumbs. Before looping, be sure
 * to `rewind()` to reset the internal index.
 */
class Crumbs implements CrumbCollection
{
	/**
	 * Stores the crumb instances with sequential keys.
	 */
	protected array $crumbs = [];

	/**
	 * Stores the crumb types with sequential keys.
	 */
	protected array $types = [];

	/**
	 * Stores the current index of the iterator.
	 */
	protected int $index = 0;

	/**
	 * Checks if the current index is valid. Use when iterating.
	 */
	public function valid(): bool
	{
		return isset($this->types[$this->index]);
	}

	/**
	 * Returns the current key (crumb type in this case) of the current
	 * crumb. Use when iterating.
	 */
	public function key(): mixed
	{
		return $this->types[$this->index] ?? null;
	}

	/**
	 * Returns the current crumb. Use when iterating.
	 */
	public function current(): ?Crumb
	{
		return $this->crumbs[$this->index] ?? null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function currentType(): ?string
	{
		return $this->types[$this->index] ?? null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function currentIsType(string $type): bool
	{
		return $type === $this->currentType();
	}

	/**
	 * Move forward to the next crumb. Use when iterating.
	 */
	public function next(): void
	{
		$this->index++;
	}

	/**
	 * Rewind the iterator to the first crumb.
	 */
	public function rewind(): void
	{
		$this->index = 0;
	}

	/**
	 * Returns the total count of crumbs in the collection.
	 */
	public function count(): int
	{
		return count($this->crumbs);
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
		return $this->position() === 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isLast(): bool
	{
		return $this->position() === $this->count();
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
	public function set(string $type, Crumb $crumb): void
	{
		$this->crumbs[] = $crumb;
		$this->types[]  = $type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove(string $type): void
	{
		foreach ($this->types as $index => $storedType) {
			if ($storedType === $type) {
				unset($this->crumbs[$index]);
				unset($this->types[$index]);
			}
		}

		$this->reindex();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $type): ?Crumb
	{
		$index = array_search($type, $this->types, true);
		return $index !== false ? $this->crumbs[$index] : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists(mixed $offset): bool
	{
		return in_array($offset, $this->types, true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetGet(mixed $offset): ?Crumb
	{
		return $this->get($offset);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->set($offset, $value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->remove($offset);
	}

	/**
	 * Re-indexes arrays to maintain sequential keys.
	 */
	protected function reindex(): void
	{
		$this->crumbs = array_values($this->crumbs);
		$this->types  = array_values($this->types);
	}
}
