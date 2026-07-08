<?php

/**
 * Enum registrar base.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

use BackedEnum;
use ReflectionException;
use X3P0\Breadcrumbs\Packages\ClassRegistry\Registry;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Seeds a class registry with the cases of a backed enum on boot. Each case maps
 * its string value (the key) to a class name via `className()`, skipping any key
 * already registered so third-party overrides survive. Subclasses name the enum
 * with an `ENUM` constant and type-hint their concrete registry in the
 * constructor so the container injects the right one.
 *
 * @internal This is an internal implementation detail of the plugin, not part
 *           of its public API. Its signature may change or it may be removed at
 *           any time without notice; third-party code should not extend or
 *           type-hint against it directly.
 */
abstract class EnumRegistrar implements Bootable
{
	/**
	 * The enum whose cases seed the registry.
	 *
	 * @var  class-string<BackedEnum&ClassEnum>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ENUM = '';

	/**
	 * Stores the registry the enum's cases are seeded into.
	 */
	public function __construct(
		protected readonly Registry $registry
	) {}

	/**
	 * @inheritDoc
	 *
	 * @throws ReflectionException
	 */
	public function boot(): void
	{
		foreach (static::ENUM::cases() as $type) {
			if (! $this->registry->isRegistered($type->value)) {
				$this->registry->register($type->value, $type->className());
			}
		}
	}
}
