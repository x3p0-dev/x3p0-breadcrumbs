<?php

/**
 * Icon renderer.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * Renders an icon attribute value — the kind `IconResolver` returns — as
 * real markup: a built-in text/glyph character, or an icon fetched from the
 * registered icon library.
 */
final class IconRenderer
{
	/**
	 * Built-in text/glyph icon values mapped to their literal character.
	 * These aren't SVG files and so can't be registered icons.
	 *
	 * @var  array<string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const TEXT_ICONS = [
		'text-slash'        => '/',
		'text-bar'          => '|',
		'text-middot'       => '·',
		'text-black-circle' => '●',
		'text-white-circle' => '○'
	];

	/**
	 * Deprecated pre-7.1 icon keys mapped to their current
	 * `{collection}/{name}` reference in the registered icon library.
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
	 * Renders an icon value as markup, or an empty string when it names no
	 * icon. Text/glyph icons are checked first, then the library, a
	 * reference to which is recognized by its `/` — which no built-in or
	 * deprecated key contains.
	 */
	public function render(string $value): string
	{
		if (isset(self::TEXT_ICONS[$value])) {
			return self::TEXT_ICONS[$value];
		}

		$value = self::DEPRECATED_ICONS[$value] ?? $value;

		if (! str_contains($value, '/')) {
			return '';
		}

		return wp_get_icon($value) ?: '';
	}
}
