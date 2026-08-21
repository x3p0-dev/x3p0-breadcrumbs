<?php

/**
 * WooCommerce shop crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Crumb;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Icon\IconOptionKey;

/**
 * Crumb representing the WooCommerce shop. It replaces the product post type
 * archive crumb wherever it appears — the shop page, single products, product
 * taxonomy archives, and as the root of the cart, checkout, and account trails.
 * Its label and URL come from the configured shop page, falling back to the
 * decorated crumb when no shop page is set.
 */
final class Shop extends Crumb
{
	/**
	 * Wraps the crumb this decorates so the label and URL can fall back to it
	 * when no shop page is configured.
	 */
	public function __construct(
		CrumbContext $context,
		private readonly Crumb $decoratedCrumb
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 0 < wc_get_page_id('shop')
			? 'woocommerce-shop'
			: $this->decoratedCrumb->getSlug();
	}

	/**
	 * Resolves the decorated crumb's own option, always. Unlike the slug,
	 * label, and URL — which come from the configured shop page when there is
	 * one — the icon has nothing to fall back *from*: this crumb only ever
	 * decorates the product post type archive crumb, so the shop and that
	 * archive are the same thing wearing the store's name, and one option
	 * covers both. Two would put a Shop control and a Products control in the
	 * block editor that silently trade places depending on whether a shop page
	 * is set.
	 *
	 * @inheritDoc
	 */
	public function iconOptionKey(): IconOptionKey|string
	{
		return $this->decoratedCrumb->iconOptionKey();
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$shopId = wc_get_page_id('shop');

		if (0 < $shopId && $title = get_the_title($shopId)) {
			return $title;
		}

		return $this->decoratedCrumb->getLabel();
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$shopId = wc_get_page_id('shop');

		if (0 < $shopId && $url = get_permalink($shopId)) {
			return $url;
		}

		return $this->decoratedCrumb->getUrl();
	}
}
