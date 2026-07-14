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
 * Each crumb is stored alongside a type key, which need not be unique — several
 * crumbs may share a type. The public API has two retrieval axes: predicate
 * methods (`first()`, `contains()`, `filter()`, …) that match against the crumb
 * object, and type methods (`hasType()`, `firstOfType()`, …) that match against
 * the stored type key. Query methods leave the collection untouched; mutation
 * methods (`push()`, `removeType()`, `replace()`, …) act in place, since the
 * collection is the accumulator the build pipeline appends to.
 *
 * The class also tracks an iteration cursor so callers rendering the trail can
 * ask whether the current crumb is first or last while looping. Call `rewind()`
 * to reset the cursor before iterating. Array access is keyed by type string
 * (passing a non-string offset yields a type error).
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
		return $this->key();
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
	 * Checks if the collection has at least one crumb.
	 */
	public function isNotEmpty(): bool
	{
		return ! $this->isEmpty();
	}

	/**
	 * Returns the first crumb, or the first crumb that satisfies the
	 * callback when one is given. Returns null when the collection is empty
	 * or nothing matches.
	 *
	 * @param null|callable(Crumb): bool $callback
	 */
	public function first(?callable $callback = null): ?Crumb
	{
		foreach ($this->crumbs as $crumb) {
			if (null === $callback || $callback($crumb)) {
				return $crumb;
			}
		}

		return null;
	}

	/**
	 * Returns the last crumb, or the last crumb that satisfies the callback
	 * when one is given. Returns null when the collection is empty or
	 * nothing matches.
	 *
	 * @param null|callable(Crumb): bool $callback
	 */
	public function last(?callable $callback = null): ?Crumb
	{
		foreach (array_reverse($this->crumbs) as $crumb) {
			if (null === $callback || $callback($crumb)) {
				return $crumb;
			}
		}

		return null;
	}

	/**
	 * Determines if any crumb satisfies the callback.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function contains(callable $callback): bool
	{
		return null !== $this->first($callback);
	}

	/**
	 * Determines if every crumb satisfies the callback. Returns true for an
	 * empty collection.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function every(callable $callback): bool
	{
		foreach ($this->crumbs as $crumb) {
			if (! $callback($crumb)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns a new collection of the crumbs that satisfy the callback,
	 * each keeping its type key.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function filter(callable $callback): self
	{
		$collection = new self();

		foreach ($this->crumbs as $index => $crumb) {
			if ($callback($crumb)) {
				$collection->push($this->types[$index], $crumb);
			}
		}

		return $collection;
	}

	/**
	 * Returns a new collection of the crumbs that do not satisfy the
	 * callback, each keeping its type key. The inverse of `filter()`.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function reject(callable $callback): self
	{
		return $this->filter(static fn (Crumb $crumb) => ! $callback($crumb));
	}

	/**
	 * Determines if a crumb of the given type exists in the collection.
	 */
	public function hasType(string $type): bool
	{
		return in_array($type, $this->types, true);
	}

	/**
	 * Returns the first crumb stored under the given type, or null.
	 */
	public function firstOfType(string $type): ?Crumb
	{
		$index = array_search($type, $this->types, true);

		return false !== $index ? $this->crumbs[$index] : null;
	}

	/**
	 * Returns a new collection of every crumb stored under the given type,
	 * each keeping its type key.
	 */
	public function allOfType(string $type): self
	{
		$collection = new self();

		foreach ($this->types as $index => $storedType) {
			if ($storedType === $type) {
				$collection->push($storedType, $this->crumbs[$index]);
			}
		}

		return $collection;
	}

	/**
	 * Appends a crumb to the end of the collection under the given type key.
	 */
	public function push(string $type, Crumb $crumb): void
	{
		$this->crumbs[] = $crumb;
		$this->types[]  = $type;
	}

	/**
	 * Inserts a crumb at the front of the collection under the given type key.
	 */
	public function prepend(string $type, Crumb $crumb): void
	{
		array_splice($this->crumbs, 0, 0, [$crumb]);
		array_splice($this->types, 0, 0, [$type]);
	}

	/**
	 * Inserts a crumb immediately before the target crumb, under the given
	 * type key. Does nothing when the target is not in the collection.
	 */
	public function insertBefore(Crumb $target, string $type, Crumb $crumb): void
	{
		$index = array_search($target, $this->crumbs, true);

		if (false !== $index) {
			array_splice($this->crumbs, $index, 0, [$crumb]);
			array_splice($this->types, $index, 0, [$type]);
		}
	}

	/**
	 * Inserts a crumb immediately after the target crumb, under the given type
	 * key. Does nothing when the target is not in the collection.
	 */
	public function insertAfter(Crumb $target, string $type, Crumb $crumb): void
	{
		$index = array_search($target, $this->crumbs, true);

		if (false !== $index) {
			array_splice($this->crumbs, $index + 1, 0, [$crumb]);
			array_splice($this->types, $index + 1, 0, [$type]);
		}
	}

	/**
	 * Removes every crumb stored under the given type, then re-indexes.
	 */
	public function removeType(string $type): void
	{
		foreach ($this->types as $index => $storedType) {
			if ($storedType === $type) {
				unset($this->crumbs[$index], $this->types[$index]);
			}
		}

		$this->reindex();
	}

	/**
	 * Removes every crumb that satisfies the callback, then re-indexes. The
	 * predicate counterpart to `removeType()`, which matches by type instead.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function removeWhere(callable $callback): void
	{
		foreach ($this->crumbs as $index => $crumb) {
			if ($callback($crumb)) {
				unset($this->crumbs[$index], $this->types[$index]);
			}
		}

		$this->reindex();
	}

	/**
	 * Removes and returns the last crumb, or null when the collection is
	 * empty.
	 */
	public function pop(): ?Crumb
	{
		array_pop($this->types);

		return array_pop($this->crumbs);
	}

	/**
	 * Removes and returns the first crumb, or null when the collection is
	 * empty.
	 */
	public function shift(): ?Crumb
	{
		array_shift($this->types);

		return array_shift($this->crumbs);
	}

	/**
	 * Replaces a crumb in place with another, keeping its position and type
	 * key. Does nothing when the crumb is not in the collection. Useful for
	 * relabeling a crumb after the trail is built without disturbing order.
	 */
	public function replace(Crumb $existing, Crumb $replacement): void
	{
		$index = array_search($existing, $this->crumbs, true);

		if (false !== $index) {
			$this->crumbs[$index] = $replacement;
		}
	}

	/**
	 * Replaces every crumb that satisfies the callback with the crumb the
	 * replacement callback returns for it, keeping each one's position and
	 * type key. The replacement receives the matched crumb. Useful for
	 * relabeling crumbs on the `CrumbsBuilt` event.
	 *
	 * @param callable(Crumb): bool  $callback
	 * @param callable(Crumb): Crumb $replacement
	 */
	public function replaceWhere(callable $callback, callable $replacement): void
	{
		foreach ($this->crumbs as $index => $crumb) {
			if ($callback($crumb)) {
				$this->crumbs[$index] = $replacement($crumb);
			}
		}
	}

	/**
	 * Maps each crumb through the callback and returns the results as a
	 * plain array, since the mapped values may no longer be crumbs.
	 *
	 * @param  callable(Crumb): mixed $callback
	 * @return array<int, mixed>
	 */
	public function map(callable $callback): array
	{
		return array_map($callback, $this->crumbs);
	}

	/**
	 * Reduces the collection to a single value.
	 *
	 * @param callable(mixed, Crumb): mixed $callback
	 */
	public function reduce(callable $callback, mixed $initial = null): mixed
	{
		return array_reduce($this->crumbs, $callback, $initial);
	}

	/**
	 * Returns the underlying crumbs as a plain, sequentially-keyed array.
	 *
	 * @return Crumb[]
	 */
	public function all(): array
	{
		return $this->crumbs;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists(mixed $offset): bool
	{
		return $this->hasType($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet(mixed $offset): ?Crumb
	{
		return $this->firstOfType($offset);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->push($offset, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset(mixed $offset): void
	{
		$this->removeType($offset);
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
