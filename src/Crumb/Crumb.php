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
	 * Key of the option holding the icon a crumb falls back on when nothing
	 * more specific resolves — a crumb whose own key nothing is registered
	 * under, such as the archive crumb of a post type that declares no
	 * archive. Held in the registry like every other icon the plugin renders,
	 * rather than as a literal here, so it is retargetable on the same terms
	 * and there is one place to read what a trail can put on screen.
	 *
	 * @var string
	 */
	private const FALLBACK_OPTION = 'fallback';

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
	public function iconOptionKey(): string
	{
		return $this->getSlug();
	}

	/**
	 * Returns the crumb's icon attribute value (e.g., a built-in text/glyph
	 * key or a `{collection}/{name}` icon library reference), left for the
	 * `Markup` layer to resolve to real markup. Whether a crumb's icon is
	 * actually shown is controlled separately, by the `Markup` layer's icon
	 * visibility setting.
	 *
	 * This states the resolution order once, for every crumb type, in
	 * descending order of how explicit each source is:
	 *
	 * 1. `explicitIcon()` — a choice the site owner made against this exact
	 *    crumb, which outranks even their configured option.
	 * 2. The icon configured for this crumb's option key.
	 * 3. `fallbackIcon()` — a type-specific guess better than the registered
	 *    default, but still only a guess, so it yields to the configured icon.
	 * 4. The default registered for the option key.
	 * 5. The default registered for the generic fallback option, so a crumb
	 *    whose key nothing is registered under still renders with something.
	 *
	 * Subclasses contribute through the two seams rather than overriding this
	 * method: a type that reorders the chain locally puts its own defaults
	 * ahead of the site owner's, and a decorating crumb cannot inherit steps
	 * buried in an override (see `Extension\WooCommerce\Crumb\StorePage`).
	 */
	final public function getIcon(): string
	{
		return $this->explicitIcon()
			?: $this->context->iconConfig->getIcon($this->iconOptionKey())
			?: $this->fallbackIcon()
			?: $this->context->iconOptions->icon($this->iconOptionKey())
			?: $this->context->iconOptions->icon(self::FALLBACK_OPTION);
	}

	/**
	 * Returns an icon the site owner set against this specific crumb — the
	 * only thing that outranks the icon they configured for its option (e.g.,
	 * `Post` returns the icon stored in the post's own meta). Empty for most
	 * types, which have no such per-instance choice to read.
	 */
	protected function explicitIcon(): string
	{
		return '';
	}

	/**
	 * Returns a type-specific icon to use ahead of the option's registered
	 * default, for a crumb that can derive something better than the generic
	 * default but has nothing an option key could be registered against
	 * (e.g., `Post` derives one from an attachment's mime type). Sits behind
	 * the icon config, since it is still only a derived guess.
	 */
	protected function fallbackIcon(): string
	{
		return '';
	}
}
