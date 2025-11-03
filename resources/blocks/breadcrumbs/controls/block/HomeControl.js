/**
 * Home control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { SymbolPicker, ToolbarDropdown } from '../../components';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { home as controlIcon } from '@wordpress/icons';
import { useEffect } from '@wordpress/element';
import { Flex, ToggleControl } from '@wordpress/components';
import { G, Path, Rect, SVG } from "@wordpress/primitives";

/**
 * Stores the available home icon options.
 */
const ICON_OPTIONS = [
	{
		value: '',
		label: __('None', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<Path d="M0 0h24v24H0z" fill="none"/>
				<Path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zM4 12c0-4.4 3.6-8 8-8 1.8 0 3.5.6 4.9 1.7L5.7 16.9C4.6 15.5 4 13.8 4 12zm8 8c-1.8 0-3.5-.6-4.9-1.7L18.3 7.1C19.4 8.5 20 10.2 20 12c0 4.4-3.6 8-8 8z"/>
			</SVG>
		)
	},
	{
		value: 'svg-outline',
		label: __('Home: Outlined', 'x3p0-breadcrumbs'),
		icon:  (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<Path d="M0 0h24v24H0V0z" fill="none"/>
				<Path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/>
			</SVG>
		)
	},
	{
		value: 'svg-fill',
		label: __('Home: Filled', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<Path d="M0 0h24v24H0z" fill="none"/>
				<Path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
			</SVG>
		)
	},
	{
		value: 'svg-house-outline',
		label: __('House: Outlined', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<G>
					<Rect fill="none" height="24" width="24"/>
				</G>
				<G><G>
					<Path d="M19,9.3V4h-3v2.6L12,3L2,12h3v8h6v-6h2v6h6v-8h3L19,9.3z M17,18h-2v-6H9v6H7v-7.81l5-4.5l5,4.5V18z"/>
					<Path d="M10,10h4c0-1.1-0.9-2-2-2S10,8.9,10,10z"/>
				</G></G>
			</SVG>
		)
	},
	{
		value: 'svg-house-fill',
		label: __('House: Filled', 'x3p0-breadcrumbs'),
		icon: (
			<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
				<G>
					<Rect fill="none" height="24" width="24"/>
				</G>
				<G>
					<Path d="M19,9.3V4h-3v2.6L12,3L2,12h3v8h5v-6h4v6h5v-8h3L19,9.3z M10,10c0-1.1,0.9-2,2-2s2,0.9,2,2H10z"/>
				</G>
			</SVG>
		)
	},
	{
		value: 'text-🏠',
		label: __('Emoji: House', 'x3p0-breadcrumbs'),
		icon: '🏠'
	},
	{
		value: 'text-🏡',
		label: __('Emoji: House Garden', 'x3p0-breadcrumbs'),
		icon: '🏡'
	},
	{
		value: 'text-🏘',
		label: __('Emoji: Houses', 'x3p0-breadcrumbs'),
		icon: '🏘'
	}
];

/**
 * Renders the home icon and related controls.
 * @param props
 * @returns {JSX.Element|null}
 */
const HomeControl = ({
	attributes: {
		homeIcon,
		showHomeLabel,
		showTrailStart
	},
	setAttributes
}) => {
	useEffect(() => {
		if (! showHomeLabel && ! homeIcon) {
			setAttributes({ showHomeLabel: true });
		}
	}, [ homeIcon, showHomeLabel ]);

	if (! showTrailStart) {
		return null;
	}

	return (
		<ToolbarDropdown
			value={ homeIcon }
			label={ __('Home Icon', 'x3p0-breadcrumbs') }
			icon={ controlIcon }
		>
			<Flex direction="column" gap="4">
				<SymbolPicker
					value={ homeIcon }
					onChange={ (value) => setAttributes({ homeIcon: value }) }
					options={ ICON_OPTIONS }
					label={ __('Home Icon', 'x3p0-breadcrumbs') }
					description={ __('Pick an icon or symbol for the home breadcrumb item.', 'x3p0-breadcrumbs') }
				/>
				<ToggleControl
					label={ __('Show home label', 'x3p0-breadcrumbs') }
					checked={ showHomeLabel }
					onChange={ () => setAttributes({
						showHomeLabel: ! showHomeLabel
					}) }
					disabled={ ! homeIcon }
					__nextHasNoMarginBottom
				/>
			</Flex>
		</ToolbarDropdown>
	);
};

export default HomeControl;
