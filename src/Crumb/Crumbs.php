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
 * Returns an iterable collection of crumb items.
 */
class Crumbs implements CrumbCollection
{
	/**
	 * Stores the array of crumb instances in the collection.
	 */
	protected array $crumbs = [];

	/**
	 * Stores the crumb types for iteration with 0-indexed keys.
	 */
	protected array $types = [];

	/**
	 * Stores the current index of the iterator.
	 */
	protected int $index = 0;

	/**
	 * {@inheritDoc}
	 */
	public function valid(): bool
	{
		return isset($this->types[$this->index]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function key(): mixed
	{
		return $this->types[$this->index] ?? null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function current(): mixed
	{
		$type = $this->currentType();
		return $type !== null ? $this->crumbs[$type] : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function currentType(): string|null
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
		$this->types = array_keys($this->crumbs);
		$this->index = 0;
	}

	/**
	 * {@inheritDoc}
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
		$this->crumbs[$type] = $crumb;
		$this->resetTypes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove(string $type): void
	{
		unset($this->crumbs[$type]);
		$this->resetTypes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $type): ?Crumb
	{
		return $this->crumbs[$type] ?? null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists(mixed $offset): bool
	{
		return isset($this->crumbs[$offset]);
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
	 * Resets keys.
	 */
	protected function resetTypes(): void
	{
		$this->types = [];
	}
}
