<?php

/**
 * Crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsConfig;

/**
 * Abstract base for a single item in the breadcrumb trail. A crumb is created
 * by a `Query` or `Assembler` and exposes everything needed to output the item:
 * a text label and, optionally, a URL. Concrete crumbs live under `Type` and
 * read shared state from the supplied `CrumbContext`. This class is the
 * contract that the rest of the system typehints against; subclasses must
 * implement `getSlug()` and `getLabel()` (and override `getUrl()` where the
 * crumb links somewhere).
 */
abstract class Crumb
{
	/**
	 * Read-only trail config, re-exposed from the context so concrete types
	 * can read the thing they reach for most (labels and the like) directly,
	 * without going through `$this->context` — mirroring how
	 * `AssemblerContext` re-exposes it from `CrumbBuilder`.
	 */
	protected readonly BreadcrumbsConfig $config;

	/**
	 * Stores the shared context every crumb reads from, then re-exposes its
	 * trail config for direct access.
	 */
	public function __construct(protected readonly CrumbContext $context)
	{
		$this->config = $context->config;
	}

	/**
	 * Returns the crumb's type slug, used for its `crumb--{slug}` CSS class
	 * and to match it in the collection. Every concrete crumb must return a
	 * stable slug. Preferably, it would be kebab-cased (e.g., `post-type`).
	 * And prefixed for third-party crumbs (e.g., `woocommerce-shop`).
	 */
	abstract public function getSlug(): string;

	/**
	 * Returns the internationalized text label shown for the crumb.
	 */
	abstract public function getLabel(): string;

	/**
	 * Returns the crumb URL, or an empty string when the crumb is not a link.
	 */
	public function getUrl(): string
	{
		return '';
	}

	/**
	 * Returns the key of the icon option this crumb pulls its icon from —
	 * the lookup key for both a site-owner override and a registered default
	 * (see `BreadcrumbsConfig::getIcon()`). Defaults to the crumb's own
	 * slug, so most types declare nothing; a family sharing one option
	 * overrides this once on its base class (e.g., `Date` returns `date`),
	 * and the dynamically-keyed types compute it (e.g., `Post` returns
	 * `post-type:{$type}`).
	 */
	public function iconKey(): string
	{
		return $this->getSlug();
	}

	/**
	 * Returns the crumb's icon attribute value (e.g., a built-in text/glyph
	 * key or a `{collection}/{name}` icon library reference), left for the
	 * `Markup` layer to resolve to real markup. The crumb's icon key first
	 * resolves to the caller's choice from the icon config, then to the
	 * default registered for it in the icon options registry; the generic
	 * value here is the last resort, so a crumb never renders with no icon
	 * at all once icons are shown for it. Whether a crumb's icon is actually
	 * shown is controlled separately, by the `Markup` layer's icon
	 * visibility setting.
	 */
	public function getIcon(): string
	{
		return $this->context->iconConfig->getIcon($this->iconKey())
			?: $this->context->iconOptions->icon($this->iconKey())
			?: 'x3p0-breadcrumbs/article';
	}
}
