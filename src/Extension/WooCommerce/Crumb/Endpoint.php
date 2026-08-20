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

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\Endpoint as EndpointSlug;

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
		CrumbContext $context,
		public readonly string $endpoint
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'woocommerce-endpoint';
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

	/**
	 * Resolves the option registered for this specific endpoint when it is one
	 * the plugin has an opinion about, so each carries its own default and can
	 * be configured on its own. Every other endpoint falls back to the slug —
	 * the shared `woocommerce-endpoint` option, which applies uniformly to all
	 * of them.
	 *
	 * @inheritDoc
	 */
	public function iconOptionKey(): string
	{
		return EndpointSlug::tryFrom($this->endpoint)?->optionKey() ?? $this->getSlug();
	}
}
