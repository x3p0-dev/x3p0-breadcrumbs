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

use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\Post as PostCrumb;
use X3P0\Breadcrumbs\Crumb\Type\PostType as PostTypeCrumb;
use X3P0\Breadcrumbs\Extension\Extension;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Shop as ShopCrumb;
use X3P0\Breadcrumbs\Icon\Event\IconOptionsRegistered;
use X3P0\Breadcrumbs\Icon\IconOption;
use X3P0\Breadcrumbs\Packages\Event\Listener\Listenable;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

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
	/**
	 * Icons for the store pages (Cart, Checkout, My Account), keyed by the
	 * page type `wc_get_page_id()` accepts. These are ordinary `page`-type
	 * posts with no post-type-level signal of their own, so the icon has to
	 * be attached here rather than resolved from a post-type default.
	 *
	 * @var  array<string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const PAGE_ICONS = [
		'cart'      => 'core/cart',
		'checkout'  => 'core/payment',
		'myaccount' => 'core/people'
	];

	/**
	 * @inheritDoc
	 */
	public function subscribeTo(Listenable $registry): void
	{
		$registry->listenTo($this->onIconOptionsRegistered(...));
		$registry->listenTo($this->onQueryTypeResolving(...));
		$registry->listenTo($this->onCrumbsBuilt(...));
	}

	/**
	 * Registers the default icons for WooCommerce's own icon option keys. The
	 * product post type and its taxonomies are already registered by the time
	 * this runs — they are an ordinary public post type and taxonomies — so
	 * they are retargeted with `setIcon()`, which swaps the icon and leaves
	 * the label the registrar derived from each object in place. The
	 * extension's own crumb types have no such counterpart and are registered
	 * outright, unlabeled: they carry a default icon only and are not offered
	 * as block controls.
	 */
	public function onIconOptionsRegistered(IconOptionsRegistered $event): void
	{
		$event->options->setIcon(IconOption::postTypeKey('product'), 'x3p0-breadcrumbs/package');
		$event->options->setIcon(IconOption::taxonomyKey('product_cat'), 'core/category');
		$event->options->setIcon(IconOption::taxonomyKey('product_tag'), 'core/tag');

		$event->options->add(
			new IconOption('woocommerce-shop', 'core/store'),
			new IconOption('woocommerce-endpoint', 'core/more-vertical')
		);
	}

	/**
	 * Reroutes the endpoint-bearing store pages to their custom query
	 * before the built-in singular query would claim them, then stops
	 * propagation to keep the final say. Everything else — including the
	 * shop, products, and product taxonomies, which the base queries handle
	 * — falls through untouched.
	 */
	public function onQueryTypeResolving(QueryTypeResolving $event): void
	{
		$type = match (true) {
			is_account_page() => Query\Account::class,
			is_cart()         => Query\Cart::class,
			is_checkout()     => Query\Checkout::class,
			default           => null
		};

		if ($type) {
			$event->queryType = $type;
			$event->stopPropagation();
		}
	}

	/**
	 * When the shop page is the site's front page, first removes the
	 * product post type archive crumb entirely, since the home crumb
	 * already represents it. Otherwise, replaces that crumb with the shop
	 * crumb wherever it appears, so the archive reads as the shop without
	 * overriding the built-in post type crumb class. Then attaches icons to
	 * the store pages, which — unlike a product or its taxonomy terms — are
	 * ordinary `page`-type posts with no post-type-level signal to derive a
	 * default icon from. Endpoint crumbs need no such step; `Endpoint` is
	 * WooCommerce's own class and resolves its own icon from its endpoint
	 * key directly, the same way it already resolves its own label.
	 */
	public function onCrumbsBuilt(CrumbsBuilt $event): void
	{
		if ($this->shopIsFrontPage()) {
			$event->crumbs->removeInstanceWhere(
				PostTypeCrumb::class,
				static fn (PostTypeCrumb $crumb) => 'product' === $crumb->postType->name
			);
		}

		$event->crumbs->replaceInstanceWhere(
			PostTypeCrumb::class,
			static fn (PostTypeCrumb $crumb) => 'product' === $crumb->postType->name,
			static fn (PostTypeCrumb $crumb) => $event->makeCrumb(
				ShopCrumb::class,
				['decoratedCrumb' => $crumb]
			)
		);

		$this->applyPageIcons($event);
	}

	/**
	 * Attaches an icon to the Cart, Checkout, and My Account page crumbs —
	 * matched by post ID, since each is just an ordinary `page`-type post
	 * the site owner configured under WooCommerce's settings.
	 */
	private function applyPageIcons(CrumbsBuilt $event): void
	{
		foreach (self::PAGE_ICONS as $page => $icon) {
			$pageId = absint(wc_get_page_id($page));

			if (0 >= $pageId) {
				continue;
			}

			$event->crumbs->replaceInstanceWhere(
				PostCrumb::class,
				static fn (PostCrumb $crumb) => $crumb->post->ID === $pageId,
				static fn (PostCrumb $crumb) => $event->makeCrumb(PostCrumb::class, [
					'post' => $crumb->post,
					'icon' => $icon
				])
			);
		}
	}

	/**
	 * Whether the shop page is configured as the site's static front page.
	 */
	private function shopIsFrontPage(): bool
	{
		return 'posts' !== get_option('show_on_front')
			&& absint(wc_get_page_id('shop')) === absint(get_option('page_on_front'));
	}
}
