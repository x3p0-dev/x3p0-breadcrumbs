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

use Countable;
use Iterator;

/**
 * Ordered, iterable collection of the crumbs that make up a breadcrumb trail.
 * The collection stores only the crumbs themselves. The public API has two
 * retrieval axes: predicate methods (`first()`, `contains()`, `filter()`, …)
 * that match against the crumb object, and type methods (`hasType()`,
 * `firstOfType()`, …) that match against the crumb's type slug. Query methods
 * leave the collection untouched; mutation methods (`push()`, `removeType()`,
 * `replace()`, …) act in place, since the collection is the accumulator the
 * build pipeline appends to.
 *
 * The class also tracks an iteration cursor so callers rendering the trail can
 * ask whether the current crumb is first or last while looping. Call `rewind()`
 * to reset the cursor before iterating.
 */
final class CrumbCollection implements Iterator, Countable
{
	/**
	 * Stores the crumb instances with sequential keys.
	 * @var Crumb[]
	 */
	private array $crumbs = [];

	/**
	 * Stores the current index of the iterator.
	 */
	private int $index = 0;

	/**
	 * Checks if the current index is valid. Use when iterating.
	 */
	public function valid(): bool
	{
		return isset($this->crumbs[$this->index]);
	}

	/**
	 * @inheritDoc
	 */
	public function key(): int
	{
		return $this->index;
	}

	/**
	 * Returns the current crumb. Use when iterating.
	 */
	public function current(): ?Crumb
	{
		return $this->crumbs[$this->index] ?? null;
	}

	/**
	 * Returns the type slug of the current crumb without advancing the
	 * iterator.
	 *
	 * @deprecated Read the type from the crumb directly via `Crumb::type()`.
	 */
	public function currentType(): ?string
	{
		_deprecated_function(__METHOD__, '5.0.0', Crumb::class . '::getType()');
		return $this->current()?->getType();
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
	 * Returns a new collection of the crumbs that satisfy the callback.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function filter(callable $callback): self
	{
		$collection = new self();

		foreach ($this->crumbs as $crumb) {
			if ($callback($crumb)) {
				$collection->push($crumb);
			}
		}

		return $collection;
	}

	/**
	 * Returns a new collection of the crumbs that do not satisfy the
	 * callback. The inverse of `filter()`.
	 *
	 * @param callable(Crumb): bool $callback
	 */
	public function reject(callable $callback): self
	{
		return $this->filter(static fn (Crumb $crumb) => ! $callback($crumb));
	}

	/**
	 * Determines if a crumb of the given type slug exists in the collection.
	 */
	public function hasType(string $type): bool
	{
		return $this->contains(static fn (Crumb $crumb) => $crumb->getType() === $type);
	}

	/**
	 * Returns the first crumb with the given type slug, or null.
	 */
	public function firstOfType(string $type): ?Crumb
	{
		return $this->first(static fn (Crumb $crumb) => $crumb->getType() === $type);
	}

	/**
	 * Returns the last crumb with the given type slug, or null.
	 */
	public function lastOfType(string $type): ?Crumb
	{
		return $this->last(static fn (Crumb $crumb) => $crumb->getType() === $type);
	}

	/**
	 * Returns a new collection of every crumb with the given type slug.
	 */
	public function allOfType(string $type): self
	{
		return $this->filter(static fn (Crumb $crumb) => $crumb->getType() === $type);
	}

	/**
	 * Appends a crumb to the end of the collection.
	 */
	public function push(Crumb $crumb): void
	{
		$this->crumbs[] = $crumb;
	}

	/**
	 * Inserts a crumb at the front of the collection.
	 */
	public function prepend(Crumb $crumb): void
	{
		array_unshift($this->crumbs, $crumb);
	}

	/**
	 * Inserts a crumb immediately before the target crumb. Does nothing
	 * when the target is not in the collection.
	 */
	public function insertBefore(Crumb $target, Crumb $crumb): void
	{
		$index = array_search($target, $this->crumbs, true);

		if (false !== $index) {
			array_splice($this->crumbs, $index, 0, [$crumb]);
		}
	}

	/**
	 * Inserts a crumb immediately after the target crumb. Does nothing when
	 * the target is not in the collection.
	 */
	public function insertAfter(Crumb $target, Crumb $crumb): void
	{
		$index = array_search($target, $this->crumbs, true);

		if (false !== $index) {
			array_splice($this->crumbs, $index + 1, 0, [$crumb]);
		}
	}

	/**
	 * Removes every crumb with the given type slug, then re-indexes.
	 */
	public function removeType(string $type): void
	{
		$this->removeWhere(static fn (Crumb $crumb) => $crumb->getType() === $type);
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
				unset($this->crumbs[$index]);
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
		return array_pop($this->crumbs);
	}

	/**
	 * Removes and returns the first crumb, or null when the collection is
	 * empty.
	 */
	public function shift(): ?Crumb
	{
		return array_shift($this->crumbs);
	}

	/**
	 * Replaces a crumb in place with another, keeping its position. Does
	 * nothing when the crumb is not in the collection. Useful for relabeling
	 * a crumb after the trail is built without disturbing order.
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
	 * replacement callback returns for it, keeping each one's position. The
	 * replacement receives the matched crumb. Useful for relabeling crumbs
	 * on the `CrumbsBuilt` event.
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
	 * Re-indexes the crumbs to maintain sequential keys.
	 */
	private function reindex(): void
	{
		$this->crumbs = array_values($this->crumbs);
	}
}
