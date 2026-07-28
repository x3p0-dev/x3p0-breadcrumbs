<?php

/**
 * Extension type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension;

use X3P0\Breadcrumbs\Contracts\EnumDefinition;
use X3P0\Breadcrumbs\Extension\SenseiLms\SenseiLms;
use X3P0\Breadcrumbs\Extension\WooCommerce\WooCommerce;

/**
 * The canonical built-in extension types. This enum is used for binding these
 * extensions to the container when they are active.
 *
 * @internal The enum itself is an internal implementation detail; third-party
 *           code should not use it directly.
 */
enum ExtensionType implements EnumDefinition
{
	case SenseiLms;
	case WooCommerce;

	/**
	 * @inheritDoc
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::SenseiLms   => SenseiLms::class,
			self::WooCommerce => WooCommerce::class
		};
	}

	/**
	 * Returns whether this extension case is active.
	 */
	public function isActive(): bool
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::SenseiLms   => function_exists('Sensei'),
			self::WooCommerce => class_exists('WooCommerce')
		};
	}

	/**
	 * Returns an array of active extension classnames.
	 */
	public static function active(): array
	{
		return array_map(
			static fn(self $case) => $case->className(),
			array_filter(self::cases(), static fn(self $case) => $case->isActive())
		);
	}
}
