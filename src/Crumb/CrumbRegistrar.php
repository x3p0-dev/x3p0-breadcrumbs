<?php

/**
 * Crumb registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Seeds the registry with the plugin's built-in crumb types on boot. Each
 * `CrumbType` case maps to its concrete class, and existing registrations are
 * left untouched so third-party overrides take precedence.
 */
final class CrumbRegistrar implements Bootable
{
	/**
	 * Stores the registry that the built-in crumb types are seeded into.
	 */
	public function __construct(
		private readonly CrumbRegistry $registry
	) {}

	/**
	 * Registers each `CrumbType` case that has not already been registered.
	 *
	 * @inheritDoc
	 */
	public function boot(): void
	{
		foreach (CrumbType::cases() as $type) {
			if (! $this->registry->isRegistered($type->value)) {
				$this->registry->register($type->value, $type->className());
			}
		}
	}
}
