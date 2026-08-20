<?php

/**
 * WooCommerce endpoint enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Support;

/**
 * WooCommerce uses magic strings instead of a constant or enum to reference
 * its own account/checkout endpoints. This is the single source of truth for
 * the ones this plugin specifically references, shared by the `Assembler`
 * (which decides trail structure) and `Crumb` (which decides which icon option
 * it resolves) `Endpoint` classes and by the extension registering those
 * options, so the slugs live in exactly one place. Not every
 * WooCommerce endpoint needs a case here — only the ones this plugin's code
 * branches on or has an opinion about.
 */
enum Endpoint: string
{
	case Orders         = 'orders';
	case ViewOrder      = 'view-order';
	case EditAddress    = 'edit-address';
	case Downloads      = 'downloads';
	case PaymentMethods = 'payment-methods';
	case EditAccount    = 'edit-account';
	case LostPassword   = 'lost-password';
	case OrderPay       = 'order-pay';
	case OrderReceived  = 'order-received';
	case Wishlist      = 'wishlist';

	/**
	 * Returns the icon option key for this endpoint. Every endpoint crumb
	 * shares the one `woocommerce-endpoint` slug, which is all an unrecognized
	 * endpoint has to resolve an icon from; the endpoints named here get a key
	 * apiece under it, so each carries its own registered default rather than
	 * the crumb hardcoding one.
	 */
	public function optionKey(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return 'woocommerce-endpoint:' . $this->value;
	}

	/**
	 * Returns the default icon registered for this endpoint's option key.
	 */
	public function icon(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Orders, self::ViewOrder        => 'core/receipt',
			self::EditAddress                    => 'core/map-marker',
			self::Downloads                      => 'core/download',
			self::PaymentMethods, self::OrderPay => 'core/payment',
			self::EditAccount                    => 'core/pencil',
			self::LostPassword                   => 'core/key',
			self::OrderReceived                  => 'core/check',
			self::Wishlist                       => 'core/star-filled'
		};
	}
}
