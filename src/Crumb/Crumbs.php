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
use X3P0\Breadcrumbs\Support\Collection;

/**
 * Returns an iterable collection of crumb items.
 */
class Crumbs extends Collection implements CrumbCollection
{
	/**
	 * {@inheritDoc}
	 */
	public function set(?string $name, Crumb $crumb): void
	{
		if ($name === null) {
			$this->items[] = $crumb;
		} else {
			$this->items[$name] = $crumb;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $name): ?Crumb
	{
		return $this->items[$name] ?? null;
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
}
