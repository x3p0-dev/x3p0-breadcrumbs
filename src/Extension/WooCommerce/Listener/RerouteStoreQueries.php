<?php

/**
 * WooCommerce store query reroute listener.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Listener;

use X3P0\Breadcrumbs\Extension\WooCommerce\Query\Account;
use X3P0\Breadcrumbs\Extension\WooCommerce\Query\Cart;
use X3P0\Breadcrumbs\Extension\WooCommerce\Query\Checkout;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

/**
 * Reroutes the endpoint-bearing store pages — the cart, checkout, and My
 * Account pages — to their custom query. Each is an ordinary page as far as
 * WordPress is concerned, so the built-in singular query would otherwise claim
 * it and collapse its endpoints (orders, view-order, order-received, and the
 * rest, which WooCommerce serves as query vars on the host page) into the host
 * page's single crumb.
 *
 * Everything else falls through untouched, the shop, products, and product
 * taxonomies included: a product is a public post type with an archive and the
 * product taxonomies are ordinary taxonomies, so the base queries already build
 * the right trail for them.
 */
final class RerouteStoreQueries
{
	/**
	 * Claims the event for the matching store query and stops propagation to
	 * keep the final say, or leaves it alone when this is not a store page.
	 */
	public function __invoke(QueryTypeResolving $event): void
	{
		$type = match (true) {
			is_account_page() => Account::class,
			is_cart()         => Cart::class,
			is_checkout()     => Checkout::class,
			default           => null
		};

		if ($type) {
			$event->queryType = $type;
			$event->stopPropagation();
		}
	}
}
