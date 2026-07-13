<?php

/**
 * WooCommerce endpoint crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Crumb representing a WooCommerce account or checkout endpoint (orders,
 * view-order, order-received, edit-address, and the rest). Its label and URL
 * are derived from the endpoint key using WooCommerce's own endpoint title and
 * URL, so they stay in sync with WooCommerce's naming and permalink settings.
 */
final class Endpoint extends Crumb
{
	/**
	 * Stores the WooCommerce endpoint key (e.g. `orders` or `edit-address`).
	 */
	public function __construct(
		BreadcrumbsContext $context,
		public readonly string $endpoint
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return WC()->query?->get_endpoint_title($this->endpoint) ?? '';
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return wc_get_endpoint_url($this->endpoint);
	}
}
