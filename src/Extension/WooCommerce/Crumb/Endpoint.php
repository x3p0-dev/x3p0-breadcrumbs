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

use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Crumb\Crumb;
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
	 * Fallback for any endpoint not in the {@see EndpointSlug} enum.
	 */
	protected const ICON = 'core/more-vertical';

	/**
	 * Stores the WooCommerce endpoint key (e.g. `orders` or `edit-address`)
	 * and an optional icon override, for a caller that wants to bypass
	 * `endpointIcon()`'s own mapping entirely.
	 */
	public function __construct(
		BreadcrumbsConfig $config,
		public readonly string $endpoint,
		private readonly string $icon = ''
	) {
		parent::__construct(config: $config);
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
	 * Returns this endpoint's own icon override when one was explicitly set,
	 * otherwise the {@see EndpointSlug} enum's icon for this endpoint's key,
	 * otherwise the config's generic `woocommerce-endpoint` override (if a
	 * site owner set one) and then `self::ICON` — every endpoint shares the
	 * same slug, so that config key applies uniformly to all of them.
	 *
	 * @inheritDoc
	 */
	public function getIcon(): string
	{
		return $this->icon ?: (EndpointSlug::tryFrom($this->endpoint)?->icon() ?: parent::getIcon());
	}
}
