<?php

/**
 * Trail static helper class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Builder\Builder;
use X3P0\Breadcrumbs\Environment\Environment;

/**
 * Trail is a static helper class that works as an easy-to-use wrapper. This
 * class is deprecated and should no longer be used.
 *
 * @deprecated 2.0.0
 */
class Trail
{
	/**
	 * Returns a new breadcrumbs builder object.
	 */
	public static function breadcrumbs(): Contracts\Builder
	{
		return new Builder(new Environment());
	}

	/**
	 * Returns a new breadcrumbs builder after calling its `build()` method.
	 */
	public static function make(): Contracts\Builder
	{
		return static::breadcrumbs()->build();
	}

	/**
	 * Returns an array of crumb objects.
	 */
	public static function all(): array
	{
		return static::make()->getCrumbs();
	}

	/**
	 * Prints the breadcrumb trail.
	 */
	public static function display(): void
	{
		echo static::render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Renders the breadcrumb trail.
	 */
	public static function render(): string
	{
		return do_blocks('<!-- wp:x3p0/breadcrumbs /-->');
	}
}
