<?php

/**
 * WooCommerce checkout query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Query;

use X3P0\Breadcrumbs\Extension\WooCommerce\Support\StorePage as StorePageSlug;

/**
 * Builds the trail for the Checkout page and its endpoints — pay for order
 * (order-pay) and order received (order-received) — which WooCommerce serves as
 * query vars on the checkout page and which would otherwise collapse into a
 * single "Checkout" crumb.
 */
final class Checkout extends StorePage
{
	/**
	 * @inheritDoc
	 */
	protected function pageId(): int
	{
		return StorePageSlug::Checkout->pageId();
	}
}
