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
 * Registers the default assembler types with the registry.
 */
final class AssemblerRegistrar implements Bootable
{
	/**
	 * Sets up the object state.
	 */
	public function __construct(
		protected readonly AssemblerRegistry $registry
	) {}

	/**
	 * @inheritDoc
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
