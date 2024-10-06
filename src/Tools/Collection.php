<?php

/**
 * Collection class.
 *
 * This file houses the `Collection` class, which is a class used for storing
 * collections of data. Generally speaking, it was built for storing an
 * array of key/value pairs. Values can be any type of value. Keys should
 * be named rather than numeric if you need easy access.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs\Tools;

use ArrayObject;
use JsonSerializable;

class Collection extends ArrayObject implements JsonSerializable
{
	/**
	 * Add an item.
	 */
	public function add(mixed $name, mixed $value): void
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * Removes an item.
	 */
	public function remove(mixed $name): void
	{
		$this->offsetUnset($name);
	}

	/**
	 * Checks if an item exists.
	 */
	public function has(mixed $name): bool
	{
		return $this->offsetExists($name);
	}

	/**
	 * Returns an item.
	 */
	public function get(mixed $name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * Returns the collection of items.
	 */
	public function all(): array
	{
		return $this->getArrayCopy();
	}

	/**
	 * Magic method when trying to set a property. Assume the property is
	 * part of the collection and add it.
	 */
	public function __set(string $name, mixed $value): void
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * Magic method when trying to unset a property.
	 */
	public function __unset(string $name): void
	{
		$this->offsetUnset($name);
	}

	/**
	 * Magic method when trying to check if a property has.
	 */
	public function __isset(string $name): bool
	{
		return $this->offsetExists($name);
	}

	/**
	 * Magic method when trying to get a property.
	 */
	public function __get(string $name): mixed
	{
		return $this->offSetGet($name);
	}

	/**
	 * Returns a JSON-ready array of data.
	 */
	public function jsonSerialize(): mixed
	{
		return array_map(
			fn($value) => $value instanceof JsonSerializable
				? $value->jsonSerialize()
				: $value,
			$this->all()
		);
	}
}
