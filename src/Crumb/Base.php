<?php

/**
 * Crumb class.
 *
 * This is the base class, which should be sub-classed, for creating a specific
 * breadcrumb item. Each sub-class should, at minimum, have a label. Not all
 * will necessarily have a URL if they're only designed to be the final crumb in
 * the trail.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Crumb;

abstract class Base implements Crumb
{
	/**
	 * Creates a new crumb object. Any data passed in within the `$data`
	 * array will be automatically assigned to any existing properties, which
	 * can be useful for sub-classes that have custom properties.
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs
	) {}

	/**
	 * Returns the type for the crumb. By default, we just use the PHP class
	 * name to build the type.  If wanting something custom, this should be
	 * handled in a sub-class.
	 */
	public function type(): string
	{
		$class = explode('\\', get_class($this));
		$class = array_pop($class);

		$pascal = preg_split('/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/', $class, -1, PREG_SPLIT_NO_EMPTY);

		return strtolower(join('-', $pascal));
	}

	/**
	 * Returns a label for the crumb.
	 */
	public function label(): string
	{
		return '';
	}

	/**
	 * Returns a URL for the crumb.
	 */
	public function url(): string
	{
		return '';
	}

	/**
	 * Returns whether the crumb should be visually hidden on display.
	 */
	public function visuallyHidden(): bool
	{
		return false;
	}
}
