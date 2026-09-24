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
use X3P0\Breadcrumbs\Extension\Extension;
use X3P0\Breadcrumbs\Extension\WooCommerce\Listener\AddCatalogOrderCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Listener\RegisterIcons;
use X3P0\Breadcrumbs\Extension\WooCommerce\Listener\RelabelStoreCrumbs;
use X3P0\Breadcrumbs\Extension\WooCommerce\Listener\RerouteStoreQueries;
use X3P0\Breadcrumbs\Icon\Event\IconPresetsRegistered;
use X3P0\Breadcrumbs\Packages\Event\Listener\Listenable;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

/**
 * Built-in WooCommerce integration. The base queries already build correct
 * trails for the shop, single products, and product taxonomies, since a product
 * is a public post type with an archive and the product taxonomies are ordinary
 * taxonomies. What is left is the part core has no concept of, and the whole of
 * it is the listeners subscribed below — the same seam a third party would use,
 * rather than replacements for the built-in classes, so another extension can
 * act on these same events without one overriding the others.
 *
 * Each is subscribed by class name and so is built through the container only
 * when its event first fires. Each also stands alone: a site that wants one of
 * these behaviors and not another can drop the one listener, and an extension
 * can swap any of them by binding its own concrete for it. See each class for
 * what it does and why.
 */
final class WooCommerce extends Extension
{
	/**
	 * The lowest WooCommerce version this extension supports, checked by
	 * {@see \X3P0\Breadcrumbs\Extension\ExtensionType::isActive()} before
	 * any of it is bound.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const MIN_VERSION = '11.1.0';

	/**
	 * @inheritDoc
	 */
	public function subscribeTo(Listenable $registry): void
	{
		$registry->listen(QueryTypeResolving::class, RerouteStoreQueries::class);
		$registry->listen(CrumbsBuilt::class, RelabelStoreCrumbs::class);
		$registry->listen(CrumbsBuilt::class, AddCatalogOrderCrumb::class);
		$registry->listen(IconPresetsRegistered::class, RegisterIcons::class);
	}
}
