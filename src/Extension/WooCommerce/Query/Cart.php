<?php

/**
 * WooCommerce cart query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Query;

/**
 * Builds the trail for the Cart page.
 */
final class Cart extends StorePage
{
	/**
	 * @inheritDoc
	 */
	protected function pageId(): int
	{
		return wc_get_page_id('cart');
	}
}
