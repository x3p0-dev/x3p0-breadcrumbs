<?php

/**
 * Icon option group class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * A heading the block editor lists icon option controls under. Options name
 * the group they belong to by key (see `IconOption::$group`); the key belongs
 * to the {@see IconOptionGroupRegistry} the group is registered in, not to
 * the group. Immutable: to change a registered group, take a copy through
 * `withLabel()` and hand it to `IconOptionGroupRegistry::replace()`.
 */
final class IconOptionGroup
{
	/**
	 * Sets up the group with its translated heading.
	 */
	public function __construct(public readonly string $label)
	{}

	/**
	 * Returns a copy of the group with a different heading.
	 */
	public function withLabel(string $label): self
	{
		return new self($label);
	}
}
