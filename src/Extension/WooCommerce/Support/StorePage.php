<?php

/**
 * WooCommerce store page enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Support;

use X3P0\Breadcrumbs\Icon\Icon;

/**
 * The WooCommerce store pages this plugin gives a crumb of its own, each named
 * by the key `wc_get_page_id()` accepts. As with {@see Endpoint} and
 * {@see CatalogOrder}, this is the single source of truth for those keys,
 * shared by the `StorePage` query and crumb classes and by the extension
 * registering their icon options.
 *
 * Unlike either of those, a store page is not a view WooCommerce serves itself
 * but an ordinary `page`-type post the site owner picked under WooCommerce's
 * settings. Nothing about the post says which page it is, which is why a trail
 * is matched to one by post ID, and why each page carries a flat icon option
 * key of its own rather than a suffix under a shared one — there is no
 * unrecognized store page for a shared option to catch.
 */
enum StorePage: string
{
	case Cart      = 'cart';
	case Checkout  = 'checkout';
	case MyAccount = 'myaccount';
	case Terms     = 'terms';

	/**
	 * Returns the ID of the page the store owner configured for this key, or
	 * `-1` when they configured none — `wc_get_page_id()`'s own signal for
	 * it, passed through as it comes. Note that it is deliberately not run
	 * through `absint()`, which would turn that -1 into a 1 and quietly point
	 * the caller at whatever post holds that ID.
	 */
	public function pageId(): int
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return wc_get_page_id($this->value);
	}

	/**
	 * Returns the key of the icon option this page's crumb resolves. Unlike
	 * {@see Endpoint} and {@see CatalogOrder}, whose crumbs each carry one
	 * shared slug and key their cases beneath it, a store page has no shared
	 * option to sit under and so is its own key — which is why this doubles
	 * as the crumb's slug, the seam `Crumb\StorePage` leaves
	 * `iconOptionKey()` at its default in order to ride.
	 *
	 * Not to be read as the page's slug: that is the `post_name` of whichever
	 * page the store owner configured, which this has nothing to do with.
	 */
	public function optionKey(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return 'woocommerce-' . $this->value;
	}

	/**
	 * Returns the default icon registered for this page's option key.
	 */
	public function icon(): Icon|string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Cart      => 'core/cart',
			self::Checkout  => 'core/payment',
			self::MyAccount => 'core/people',
			self::Terms     => Icon::List
		};
	}
}
