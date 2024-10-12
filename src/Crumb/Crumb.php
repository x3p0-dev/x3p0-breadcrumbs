<?php

/**
 * Crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Contracts;

/**
 * Implements the `Crumb` interface and creates a custom crumb object.
 */
abstract class Crumb implements Contracts\Crumb
{
	/**
	 * Creates a new crumb object.
	 */
	public function __construct(
		protected Contracts\Breadcrumbs $breadcrumbs
	) {}

	/**
	 * Returns the type for the crumb. By default, we just use the PHP class
	 * name to build the type. If wanting something custom, this should be
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
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function visuallyHidden(): bool
	{
		return false;
	}
}
