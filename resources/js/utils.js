/**
 * Utility functions.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-2.0-or-later
 */

import { __ }           from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';

const SEPARATORS = [
	{
		value: 'icon-chevron',
		label: __( 'Chevron', 'x3p0-breadcrumbs' ),
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="%23000000">
				<path d="M0 0h24v24H0V0z" fill="none"/>
				<path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/>
			</svg>
		),
	},
	{
		value: 'icon-chevron-double',
		label: __( 'Double Chevron', 'x3p0-breadcrumbs' ),
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
				<path d="M6.6 6L5.4 7l4.5 5-4.5 5 1.1 1 5.5-6-5.4-6zm6 0l-1.1 1 4.5 5-4.5 5 1.1 1 5.5-6-5.5-6z">
				</path>
			</svg>
		),
	},
	{
		value: 'icon-arrow',
		label: __( 'Arrow', 'x3p0-breadcrumbs' ),
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="%23000000">
				<path d="M0 0h24v24H0V0z" fill="none"/>
				<path d="M16.01 11H4v2h12.01v3L20 12l-3.99-4v3z"/>
			</svg>
		)
	},
	{
		value: 'icon-triangle',
		label: __( 'Triangle', 'x3p0-breadcrumbs' ),
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<path d="M0 0h24v24H0V0z" fill="none"/>
				<path d="M10 17l5-5-5-5v10z"/>
			</svg>
		)
	},
	{
		value: 'slash',
		label: __( 'Forward Slash', 'x3p0-breadcrumbs' ),
		content: "/"
	},
	{
		value: 'vertical-bar',
		label: __( 'Vertical Bar', 'x3p0-breadcrumbs' ),
		content: "|"
	},
	{
		value: 'middot',
		label: __( 'Middle Dot', 'x3p0-breadcrumbs' ),
		content: "·"
	},
	{
		value: 'black-circle',
		label: __( 'Black Circle', 'x3p0-breadcrumbs' ),
		content: "●"
	},
	{
		value: 'white-circle',
		label: __( 'Circle', 'x3p0-breadcrumbs' ),
		content: "○"
	},
	{
		value: 'black-square',
		label: __( 'Black Square', 'x3p0-breadcrumbs' ),
		content: "■"
	},
	{
		value: 'white-square',
		label: __( 'White Square', 'x3p0-breadcrumbs' ),
		content: "□"
	}
];

/**
 * @description Wraps the separators in a filter hook and returns them.
 *
 * @returns {array}
 */
export const getSeparators = () => Array.from( applyFilters(
	'x3p0.breadcrumbs.separators',
	new Set( SEPARATORS )
) );
