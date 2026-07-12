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

/**
 * Crumb representing the WooCommerce shop. It replaces the product post type
 * archive crumb wherever it appears — the shop page, single products, product
 * taxonomy archives, and as the root of the cart, checkout, and account trails.
 * Its label and URL come from the configured shop page, falling back to the
 * product archive title and link when no shop page is set.
 */
final class Shop extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$shop_id = wc_get_page_id('shop');

		if (0 < $shop_id && $title = get_the_title($shop_id)) {
			return $title;
		}

		if (! $postType = get_post_type_object('product')) {
			return '';
		}

		return is_post_type_archive($postType->name)
			? post_type_archive_title('', false)
			: $postType->labels->archives;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$shop_id = wc_get_page_id('shop');

		if (0 < $shop_id && $url = get_permalink($shop_id)) {
			return $url;
		}

		return (string) get_post_type_archive_link('product');
	}
}
