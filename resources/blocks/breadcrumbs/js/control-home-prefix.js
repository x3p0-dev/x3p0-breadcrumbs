/**
 * Home Prefix component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-3.0-or-later
 */

// Internal dependencies.
import { getHomePrefixes } from './utils';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { home as controlIcon } from '@wordpress/icons';
import { useEffect } from '@wordpress/element';

import {
	BaseControl,
	Button,
	Dropdown,
	Flex,
	ToggleControl,
	ToolbarButton,
	__experimentalGrid as Grid
} from '@wordpress/components';

/**
 * @description Creates a home icon control.
 */
export default ({ homePrefix, showHomeLabel, setAttributes }) => {

	useEffect(() => {
		if (! showHomeLabel && ! homePrefix) {
			setAttributes({ showHomeLabel: true });
		}
	}, [ homePrefix ]);

	// Get the homePrefix options.
	const homePrefixes = getHomePrefixes();

	// Builds a menu item for an homePrefix.
	const homePrefixButton = (sep, index) => (
		<Button
			key={ index }
			isPressed={ homePrefix === sep.value }
			className="x3p0-breadcrumbs-sep-picker__button"
			label={ sep.label }
			showTooltip
			onClick={ () => setAttributes({
				homePrefix:     sep.value,
				homePrefixType: sep.type
			}) }
		>
			{ 'image' === sep.type ? sep.icon : (
				<span className="x3p0-breadcrumbs-sep-picker__button-text">
					{ sep.icon }
				</span>
			) }
		</Button>
	);

	// Builds an homePrefix picker in a 6-column grid.
	const homePrefixPicker = (
		<BaseControl
			className="x3p0-breadcrumbs-sep-picker"
			label={ __('Home Icon', 'x3p0-ideas') }
		>
			<div className="x3p0-breadcrumbs-sep-picker__description">
				{ __('Pick an icon or symbol for the home breadcrumb item.', 'x3p0-ideas') }
			</div>
			<Grid className="x3p0-breadcrumbs-sep-picker__grid" columns="6">
				{ homePrefixes.map(
					(sep, index) => homePrefixButton(sep, index)
				) }
			</Grid>
		</BaseControl>
	);

	const showHomeLabelControl = (
		<ToggleControl
			label={ __('Show home label', 'x3p0-breadcrumbs') }
			checked={ showHomeLabel }
			onChange={ () => setAttributes({
				showHomeLabel: ! showHomeLabel
			}) }
			disabled={ ! homePrefix }
		/>
	);

	// Returns the dropdown menu item.
	return (
		<Dropdown
			className="x3p0-breadcrumbs-sep-dropdown"
			contentClassName="x3p0-breadcrumbs-sep-popover"
			focusOnMount
			popoverProps={ {
				headerTitle: __('Home Icon', 'x3p0-ideas'),
				variant: 'toolbar'
			} }
			renderToggle={ ({ isOpen, onToggle }) => (
				<ToolbarButton
					className="x3p0-breadcrumbs-sep-dropdown__button"
					icon={ controlIcon }
					label={ __('Home Icon', 'x3p0-ideas') }
					onClick={ onToggle }
					aria-expanded={ isOpen }
					isPressed={ !! homePrefix }
				/>
			) }
			renderContent={ () => (
				<Flex direction="column" gap="4">
					{ homePrefixPicker }
					{ showHomeLabelControl }
				</Flex>
			) }
		/>
	);
};
