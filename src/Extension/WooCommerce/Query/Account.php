<?php

/**
 * WooCommerce account query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Query;

/**
 * Builds the trail for the My Account page and its endpoints (orders,
 * view-order, downloads, edit-address, payment-methods, and the rest), which
 * would otherwise collapse into a single "My Account" crumb. This is the store
 * account rather than a general user profile, so its trail is rooted at the shop
 * like the rest of the store pages.
 */
final class Account extends StorePage
{
	/**
	 * @inheritDoc
	 */
	protected function pageId(): int
	{
		return wc_get_page_id('myaccount');
	}
}
