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
	case WooCommerce;

	/**
	 * @inheritDoc
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::WooCommerce => WooCommerce::class
		};
	}

	/**
	 * Returns whether this extension case is active: the platform is present
	 * and is a version the extension supports. Note that the two `WooCommerce`
	 * names below are different classes — `class_exists('WooCommerce')` tests
	 * for WooCommerce's own global class, while `WooCommerce::MIN_VERSION`
	 * is the constant on this plugin's extension, imported above.
	 *
	 * A platform too old to support is treated exactly as an absent one, so
	 * nothing is bound and no part of the extension runs against an API it
	 * was not written for.
	 */
	public function isActive(): bool
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::WooCommerce => class_exists('WooCommerce')
				&& defined('WC_VERSION')
				&& version_compare(WC_VERSION, WooCommerce::MIN_VERSION, '>=')
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
