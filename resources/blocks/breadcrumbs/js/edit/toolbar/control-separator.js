/**
 * Separator component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { SymbolPicker, ToolbarDropdown } from '../components';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { next } from '@wordpress/icons';
import {Path, SVG} from "@wordpress/primitives";

const OPTIONS = [
	{
		value: 'svg-chevron',
		label: __('Chevron', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="%23000000">
				<Path d="M0 0h24v24H0V0z" fill="none"/>
				<Path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/>
			</SVG>
		)
	},
	{
		value: 'svg-chevron-double',
		label: __('Double Chevron', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
				<Path d="M6.6 6L5.4 7l4.5 5-4.5 5 1.1 1 5.5-6-5.4-6zm6 0l-1.1 1 4.5 5-4.5 5 1.1 1 5.5-6-5.5-6z"/>
			</SVG>
		)
	},
	{
		value: 'svg-arrow',
		label: __('Arrow', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="%23000000">
				<Path d="M0 0h24v24H0V0z" fill="none"/>
				<Path d="M16.01 11H4v2h12.01v3L20 12l-3.99-4v3z"/>
			</SVG>
		)
	},
	{
		value: 'svg-triangle',
		label: __('Triangle', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<Path d="M0 0h24v24H0V0z" fill="none"/>
				<Path d="M10 17l5-5-5-5v10z"/>
			</SVG>
		)
	},
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

export default ({ attributes: { separatorIcon }, setAttributes }) => (
	<ToolbarDropdown
		value={ separatorIcon }
		label={ __('Separator', 'x3p0-breadcrumbs') }
		icon={ next }
	>
		<SymbolPicker
			value={ separatorIcon }
			onChange={ (value) => setAttributes({ separatorIcon: value }) }
			options={ OPTIONS }
			label={ __('Separator', 'x3p0-breadcrumbs') }
			description={ __('Pick an icon or symbol that sits in between and separates breadcrumb items.', 'x3p0-breadcrumbs') }
		/>
	</ToolbarDropdown>
);
