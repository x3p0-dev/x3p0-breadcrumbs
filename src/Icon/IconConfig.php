<?php

/**
 * Icon configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use X3P0\Breadcrumbs\Support\BuildsFromArray;

/**
 * Immutable configuration object holding the caller's chosen icons, keyed by
 * icon option key. Icons are their own configuration domain — an icon option
 * isn't tied to trail building (`BreadcrumbsConfig`) or display flags
 * (`MarkupConfig`), and its consumers span both pipelines: crumbs resolve
 * their own keys, and the `Markup` layer resolves presentation keys such as
 * `separator`. Holds overrides only; the registered defaults live in the
 * `IconOptions` registry, which consumers pair this with.
 */
final class IconConfig
{
	use BuildsFromArray;

	/**
	 * Stores the caller's chosen icons as an option key → icon attribute
	 * value map (e.g., `home`, `date`, `separator`, `post-type:page`). Keyed
	 * by the raw string form, since the map arrives off a block attribute
	 * saved in post content; {@see IconOptionKey} names the keys this plugin
	 * owns among them.
	 *
	 * @param array<string, string> $icons
	 */
	public function __construct(private readonly array $icons = [])
	{}

	/**
	 * Returns the icon attribute value the caller configured for the given
	 * icon option key, or an empty string if none is configured.
	 */
	public function getIcon(IconOptionKey|string $key): string
	{
		return $this->icons[IconOptionKey::normalize($key)] ?? '';
	}
}
