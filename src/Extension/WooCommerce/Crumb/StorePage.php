<?php

/**
 * WooCommerce store page crumb.
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

/**
 * Crumb representing one of the WooCommerce store pages — the cart, checkout,
 * or My Account page. Each is an ordinary `page`-type post the site owner
 * configured under WooCommerce's settings, so the built-in post crumb already
 * has the label and URL right; what it cannot know is that the page is a
 * distinct kind of place rather than any other page, and so resolves the
 * generic `post-type:page` icon option.
 *
 * This decorates that crumb to give the page an identity of its own, the same
 * way {@see Shop} does for the product archive: the slug — and with it the icon
 * option key — becomes `woocommerce-{page}`, while the label and URL delegate
 * to the crumb being decorated.
 */
final class StorePage extends Crumb
{
	/**
	 * Stores the crumb being decorated, which the label and URL delegate to,
	 * and the store page's key as `wc_get_page_id()` accepts it (`cart`,
	 * `checkout`, or `myaccount`).
	 */
	public function __construct(
		CrumbContext $context,
		private readonly Crumb $decoratedCrumb,
		private readonly string $page
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'woocommerce-' . $this->page;
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return $this->decoratedCrumb->getLabel();
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return $this->decoratedCrumb->getUrl();
	}

	/**
	 * Carries through any icon the site owner set against the decorated
	 * crumb, so an icon chosen on the page itself still outranks this store
	 * page's configured option — decorating the crumb shouldn't quietly
	 * discard a choice made on the post behind it.
	 *
	 * @inheritDoc
	 */
	protected function explicitIcon(): string
	{
		return $this->decoratedCrumb->explicitIcon();
	}
}
