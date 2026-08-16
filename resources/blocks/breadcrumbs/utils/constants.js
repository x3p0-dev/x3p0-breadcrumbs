/**
 * Constants.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import {__} from "@wordpress/i18n";

/**
 * Stores the available separator icon options. Only the plain-text/glyph
 * choices live here — they aren't SVG files and so can't be registered
 * icons; every SVG separator now comes from the registered icon library
 * ({@see IconLibraryModal}).
 */
export const SEPARATOR_ICONS = [
	{
		value: 'text-slash',
		label: __('Slash', 'x3p0-breadcrumbs'),
		icon:  "/"
	},
	{
		value: 'text-bar',
		label: __('Vertical Bar', 'x3p0-breadcrumbs'),
		icon:  "|"
	},
	{
		value: 'text-middot',
		label: __('Middle Dot', 'x3p0-breadcrumbs'),
		icon:  "·"
	},
	{
		value: 'text-black-circle',
		label: __('Circle: Filled', 'x3p0-breadcrumbs'),
		icon:  "●"
	},
	{
		value: 'text-white-circle',
		label: __('Circle: Outlined', 'x3p0-breadcrumbs'),
		icon:  "○"
	}
];
