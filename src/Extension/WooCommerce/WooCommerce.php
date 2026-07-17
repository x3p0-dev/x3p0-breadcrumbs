<?php

/**
 * WooCommerce extension.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce;

use ReflectionException;
use X3P0\Breadcrumbs\Crumb\CrumbRegistry;
use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\PostType as PostTypeCrumb;
use X3P0\Breadcrumbs\Extension\Extension;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Endpoint as EndpointCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Shop as ShopCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Query\Account as AccountQuery;
use X3P0\Breadcrumbs\Extension\WooCommerce\Query\Cart as CartQuery;
use X3P0\Breadcrumbs\Extension\WooCommerce\Query\Checkout as CheckoutQuery;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;
use X3P0\Breadcrumbs\Query\QueryRegistry;

/**
 * Built-in WooCommerce integration. The base queries already build correct
 * trails for the shop, single products, and product taxonomies, since a product
 * is a public post type with an archive and the product taxonomies are ordinary
 * taxonomies — so the extension only relabels the product post type archive
 * crumb to read as the shop. It does this on the `CrumbsBuilt` event rather than
 * by replacing the built-in crumb class, so other extensions can relabel their
 * own crumbs on the same event without one overriding the others.
 *
 * The rest is the part core has no concept of: the cart, checkout, and My
 * Account pages, whose endpoints (orders, view-order, order-received, and the
 * rest) are query vars on the host page. Each gets a custom query, rerouted to
 * while resolving the query type, that roots the trail at the shop and adds a
 * leaf crumb for the active endpoint.
 */
final class WooCommerce extends Extension
{
	public const QUERY_ACCOUNT  = 'woocommerce/account';
	public const QUERY_CART     = 'woocommerce/cart';
	public const QUERY_CHECKOUT = 'woocommerce/checkout';
	public const CRUMB_ENDPOINT = 'woocommerce/endpoint';
	public const CRUMB_SHOP     = 'woocommerce/shop';

	/**
	 * Stores the query and crumb registries the extension seeds its custom
	 * types into.
	 */
	public function __construct(
		private readonly QueryRegistry $queries,
		private readonly CrumbRegistry $crumbs
	) {}

	/**
	 * @inheritDoc
	 */
	public function isActive(): bool
	{
		return class_exists('WooCommerce');
	}

	/**
	 * @inheritDoc
	 * @throws ReflectionException
	 */
	public function register(): void
	{
		// Register WooCommerce query types.
		$this->queries->register(self::QUERY_ACCOUNT, AccountQuery::class);
		$this->queries->register(self::QUERY_CART, CartQuery::class);
		$this->queries->register(self::QUERY_CHECKOUT, CheckoutQuery::class);

		// Register WooCommerce crumb types.
		$this->crumbs->register(self::CRUMB_ENDPOINT, EndpointCrumb::class);
		$this->crumbs->register(self::CRUMB_SHOP, ShopCrumb::class);
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array
	{
		return [
			QueryTypeResolving::class => 'resolveQueryType',
			CrumbsBuilt::class        => 'relabelShop'
		];
	}

	/**
	 * Reroutes the endpoint-bearing store pages to their custom query
	 * before the built-in singular query would claim them, then stops
	 * propagation to keep the final say. Everything else — including the
	 * shop, products, and product taxonomies, which the base queries handle
	 * — falls through untouched.
	 */
	public function resolveQueryType(QueryTypeResolving $event): void
	{
		$type = match (true) {
			is_account_page() => self::QUERY_ACCOUNT,
			is_cart()         => self::QUERY_CART,
			is_checkout()     => self::QUERY_CHECKOUT,
			default           => null
		};

		if ($type) {
			$event->setQueryType($type);
			$event->stopPropagation();
		}
	}

	/**
	 * Replaces the product post type archive crumb with the shop crumb
	 * wherever it appears, so the archive reads as the shop without
	 * overriding the built-in post type crumb class.
	 */
	public function relabelShop(CrumbsBuilt $event): void
	{
		$event->crumbs->replaceInstanceWhere(
			PostTypeCrumb::class,
			static fn (PostTypeCrumb $crumb) => 'product' === $crumb->postType->name,
			static fn (PostTypeCrumb $crumb) => $event->context->makeCrumb(self::CRUMB_SHOP, [
				'decoratedCrumb' => $crumb
			])
		);
	}
}
