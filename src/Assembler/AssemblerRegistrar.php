<?php

/**
 * Assembler registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Framework\Contracts\Bootable;

/**
 * Seeds the registry with the plugin's built-in assembler types on boot. Each
 * case of the `AssemblerType` enum is mapped to its corresponding concrete
 * class so the factory can resolve them by key.
 */
final class AssemblerRegistrar implements Bootable
{
	/**
	 * Stores the registry that the built-in assembler types are seeded into.
	 */
	public function __construct(
		protected readonly AssemblerRegistry $registry
	) {}

	/**
	 * @inheritDoc
	 *
	 * Registers each `AssemblerType` case against its class name, skipping
	 * any key that has already been registered (e.g. overridden by a third
	 * party) so existing mappings are preserved.
	 */
	public function boot(): void
	{
		foreach (AssemblerType::cases() as $type) {
			if (! $this->registry->isRegistered($type->value)) {
				$this->registry->register($type->value, $type->className());
			}
		}
	}
}
