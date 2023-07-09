/**
 * Home Icon component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-2.0-or-later
 */

// Internal dependencies.
import { getHomeIcons } from './utils';

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
 * @example
 * <HomeIconControl
 * 	attributes={props.attributes}
 * 	setAttributes={props.setAttributes}
 * 	clientId={props.clientId}
 * />
 */
export default ( { homeIcon, showHomeLabel, setAttributes } ) => {

	useEffect( () => {
		if ( ! showHomeLabel && ! homeIcon ) {
			setAttributes( {
				showHomeLabel: true
			} );
		}
	}, [ homeIcon ] );

	// Get the homeIcon options.
	const homeIcons = getHomeIcons();

	// Builds a menu item for an homeIcon.
	const homeIconButton = ( sep, index ) => (
		<Button
			key={ index }
			isPressed={ homeIcon === sep.value }
			className="x3p0-breadcrumbs-sep-picker__button"
			label={ sep.label }
			showTooltip
			onClick={ () => setAttributes( {
				homeIcon: sep.value
			} ) }
		>
			{ sep.icon ? sep.icon : (
				<span className="x3p0-breadcrumbs-sep-picker__button-text">
					{ sep.content }
				</span>
			) }
		</Button>
	);

	// Builds an homeIcon picker in a 6-column grid.
	const homeIconPicker = (
		<BaseControl
			className="x3p0-breadcrumbs-sep-picker"
			label={ __( 'Home Icon', 'x3p0-ideas' ) }
		>
			<div className="x3p0-breadcrumbs-sep-picker__description">
				{ __( 'Pick an icon or symbol for the home breadcrumb item.', 'x3p0-ideas' ) }
			</div>
			<Grid className="x3p0-breadcrumbs-sep-picker__grid" columns="6">
				{ homeIcons.map( ( sep, index ) =>
					homeIconButton( sep, index )
				) }
			</Grid>
		</BaseControl>
	);

	const showHomeLabelControl = (
		<ToggleControl
			label={ __( 'Show home label', 'x3p0-breadcrumbs' ) }
			checked={ showHomeLabel }
			onChange={ () => setAttributes( {
				showHomeLabel: ! showHomeLabel
			} ) }
			disabled={ ! homeIcon }
		/>
	)

	// Returns the dropdown menu item.
	return (
		<Dropdown
			className="x3p0-breadcrumbs-sep-dropdown"
			contentClassName="x3p0-breadcrumbs-sep-popover"
			focusOnMount
			popoverProps={ {
				headerTitle: __( 'Home Icon', 'x3p0-ideas' ),
				variant: 'toolbar'
			} }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<ToolbarButton
					className="x3p0-breadcrumbs-sep-dropdown__button"
					icon={ controlIcon }
					label={ __( 'Home Icon', 'x3p0-ideas' ) }
					onClick={ onToggle }
					aria-expanded={ isOpen }
					isPressed={ !! homeIcon }
				/>
			) }
			renderContent={ () => (
				<Flex
					direction="column"
					gap="4"
				>
					{ homeIconPicker }
					{ showHomeLabelControl }
				</Flex>
			) }
		/>
	);
};
