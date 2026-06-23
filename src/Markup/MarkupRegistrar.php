<?php

/**
 * Markup registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Framework\Contracts\Bootable;

/**
 * Seeds the registry with the built-in markup types on boot, mapping each
 * `MarkupType` enum case to its concrete class. Pre-existing registrations for
 * a key are left untouched so third-party overrides win.
 */
final class MarkupRegistrar implements Bootable
{
	/**
	 * Stores the registry the built-in types are seeded into.
	 */
	public function __construct(
		protected readonly MarkupRegistry $registry
	) {}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		foreach (MarkupType::cases() as $type) {
			if (! $this->registry->isRegistered($type->value)) {
				$this->registry->register($type->value, $type->className());
			}
		}
	}
}
