<?php

/**
 * Crumb collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Ordered, iterable collection of the crumbs that make up a breadcrumb trail.
 * Each crumb is stored alongside its type key, and the class tracks position so
 * callers can ask whether the current crumb is first or last while iterating.
 * It implements array access keyed by type string (passing a non-string offset
 * yields a type error). Call `rewind()` to reset the internal index before
 * looping.
 */
final class CrumbCollection implements ArrayAccess, Iterator, Countable
{
	/**
	 * Stores the crumb instances with sequential keys.
	 * @var Crumb[]
	 */
	private array $crumbs = [];

	/**
	 * Stores the crumb types with sequential keys.
	 * @var string[]
	 */
	private array $types = [];

	/**
	 * Stores the current index of the iterator.
	 */
	private int $index = 0;

	/**
	 * Checks if the current index is valid. Use when iterating.
	 */
	public function valid(): bool
	{
		return isset($this->types[$this->index]);
	}

	/**
	 * Returns the key of the current crumb, which is its type. Use when
	 * iterating.
	 */
	public function key(): ?string
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
	 * Returns the type key of the current crumb without advancing the
	 * iterator.
	 */
	public function currentType(): ?string
	{
		return $this->types[$this->index] ?? null;
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
	 * Returns the current position (1-indexed).
	 */
	public function position(): int
	{
		return $this->index + 1;
	}

	/**
	 * Checks if the current position is the first element.
	 */
	public function isFirst(): bool
	{
		return $this->position() === 1;
	}

	/**
	 * Checks if the current position is the last element.
	 */
	public function isLast(): bool
	{
		return $this->position() === $this->count();
	}

	/**
	 * Checks if the collection is empty.
	 */
	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	/**
	 * Appends a crumb to the end of the collection under the given type key.
	 */
	public function set(string $key, Crumb $crumb): void
	{
		$this->crumbs[] = $crumb;
		$this->types[]  = $key;
	}

	/**
	 * Removes every crumb stored under the given type key, then re-indexes.
	 */
	public function remove(string $key): void
	{
		foreach ($this->types as $index => $storedType) {
			if ($storedType === $key) {
				unset($this->crumbs[$index]);
				unset($this->types[$index]);
			}
		}

		$this->reindex();
	}

	/**
	 * Determines if a crumb type exists in the collection.
	 */
	public function has(string $key): bool
	{
		return in_array($key, $this->types, true);
	}

	/**
	 * Returns the first crumb stored under the given type key, or null.
	 */
	public function get(string $key): ?Crumb
	{
		$index = array_search($key, $this->types, true);
		return $index !== false ? $this->crumbs[$index] : null;
	}

	/**
	 * Check if any crumb of the given type has a property value that
	 * satisfies the callback.
	 *
	 * Iterates through all crumbs matching the specified type and tests
	 * each one's property value against the provided callback. Returns true
	 * on the first match found.
	 *
	 * Note: Only public/accessible properties are checked. Private/protected
	 * properties and null values are skipped automatically.
	 */
	public function hasWhere(string $key, string $property, callable $callback): bool
	{
		if (! $this->has($key)) {
			return false;
		}

		foreach (array_keys($this->types, $key, true) as $index) {
			$crumb = $this->crumbs[$index];

			if (isset($crumb->$property) && $callback($crumb->$property)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists(mixed $offset): bool
	{
		return $this->has($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet(mixed $offset): ?Crumb
	{
		return $this->get($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->set($offset, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->remove($offset);
	}

	/**
	 * Re-indexes arrays to maintain sequential keys.
	 */
	private function reindex(): void
	{
		$this->crumbs = array_values($this->crumbs);
		$this->types  = array_values($this->types);
	}
}
