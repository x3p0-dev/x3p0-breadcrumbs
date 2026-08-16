<?php

/**
 * Icon resolver.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * Resolves an icon attribute value fetched from the registered icon library,
 * remapping any deprecated pre-7.1 icon key (e.g., `svg-arrow`) to its
 * current `{collection}/{name}` reference first. Registered as a singleton
 * by `IconServiceProvider`, so the container shares one instance per request
 * rather than constructing a new one for every `Markup` type resolved.
 */
final class IconResolver
{
	/**
	 * Deprecated icons mapped to their current `{collection}/{name}`
	 * reference in the registered icon library.
	 *
	 * @var  array<string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const DEPRECATED_ICONS = [
		'svg-arrow'          => 'x3p0-breadcrumbs/arrow',
		'svg-chevron'        => 'x3p0-breadcrumbs/chevron',
		'svg-chevron-double' => 'x3p0-breadcrumbs/chevron-double',
		'svg-triangle'       => 'x3p0-breadcrumbs/triangle',
		'text-🏠'            => 'x3p0-breadcrumbs/emoji-house',
		'text-🏡'            => 'x3p0-breadcrumbs/emoji-house-garden',
		'text-🏘'            => 'x3p0-breadcrumbs/emoji-houses',
		'svg-outline'        => 'x3p0-breadcrumbs/home-outline',
		'svg-fill'           => 'x3p0-breadcrumbs/home-fill',
		'svg-house-outline'  => 'x3p0-breadcrumbs/house-outline',
		'svg-house-fill'     => 'x3p0-breadcrumbs/house-fill'
	];

	/**
	 * Resolves an icon value to real markup fetched from the registered
	 * icon library. Remaps a deprecated key to its current reference first,
	 * then looks it up by its `{collection}/{name}` identifier (recognized
	 * by containing a `/`, which no deprecated key does). Returns an empty
	 * string when the value is empty or does not resolve to a registered
	 * icon, leaving callers with no icon to render.
	 */
	public function resolve(string $value): string
	{
		if ('' === $value) {
			return '';
		}

		$value = self::DEPRECATED_ICONS[$value] ?? $value;

		if (! str_contains($value, '/')) {
			return '';
		}

		return wp_get_icon($value) ?: '';
	}
}
