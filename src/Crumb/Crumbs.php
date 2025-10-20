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

use InvalidArgumentException;
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
	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (! $value instanceof Crumb) {
			throw new InvalidArgumentException(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Item must implement %s', 'x3p0-breadcrumbs'),
				Crumb::class
			)));
		}

		if ($offset === null) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}
}
