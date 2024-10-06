<?php

/**
 * Markup interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Markup classes are responsible for rendering the final HTML for a breadcrumb
 * trail using an implementation of the `Breadcrumbs` interface.
 */
interface Markup
{
	/**
	 * Returns an escaped and ready-to-output HTML representation of the
	 * breadcrumb trail.
	 */
	public function render(): string;

	/**
	 * Returns a specific option or `null` if the option doesn't exist.
	 */
	public function option(string $name): mixed;
}
