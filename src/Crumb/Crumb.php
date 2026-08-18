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
 * read shared config from the supplied `BreadcrumbsConfig`. This class is the
 * contract that the rest of the system typehints against; subclasses must
 * implement `getSlug()` and `getLabel()` (and override `getUrl()` where the
 * crumb links somewhere).
 */
abstract class Crumb
{
	/**
	 * Icon attribute value used when neither a config nor a meta override
	 * resolves one, so a crumb never renders with no icon at all once icons
	 * are shown for it. References a core-registered icon (available since
	 * WordPress ships its own `core` icon collection), so it needs no
	 * bundling by the plugin. Concrete types redeclare this constant with an
	 * icon fitting their own semantics (e.g., `Home` uses `core/home`); this
	 * generic value is the fallback for types that don't.
	 */
	protected const ICON = 'x3p0-breadcrumbs/article';

	/**
	 * Stores the shared, read-only config.
	 */
	public function __construct(protected readonly BreadcrumbsConfig $config)
	{}

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
	 * Returns the crumb's icon attribute value (e.g., a built-in text/glyph
	 * key or a `{collection}/{name}` icon library reference), left for the
	 * `Markup` layer to resolve to real markup. Looks up the config's
	 * per-slug default (see `BreadcrumbsConfig::getIcon()`), falling back to
	 * `DEFAULT_ICON` when nothing is configured — resolved via `static::` so
	 * a concrete type's own redeclared constant is used, not this class's.
	 * Never returns an empty string — whether a crumb's icon is actually
	 * shown is controlled separately, by the `Markup` layer's icon
	 * visibility setting.
	 */
	public function getIcon(): string
	{
		return $this->config->getIcon($this->getSlug()) ?: static::ICON;
	}

	/**
	 * Returns the crumb type's built-in default icon — `static::ICON`,
	 * resolved via late static binding so a concrete type's own redeclared
	 * constant is used — without needing an instance (and, therefore,
	 * config) to get it. For contexts that want a type's default ahead of
	 * any per-slug config override, such as the block editor's canvas
	 * preview.
	 */
	public static function defaultIcon(): string
	{
		return static::ICON;
	}
}
