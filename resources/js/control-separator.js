/**
 * Separator component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-2.0-or-later
 */

// Internal dependencies.
import { getSeparators } from './utils';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { next } from '@wordpress/icons';

import {
	BaseControl,
	Button,
	Dropdown,
	ToolbarButton,
	__experimentalGrid as Grid
} from '@wordpress/components';

/**
 * @description Creates a separator separator control.
 * @example
 * <SeparatorSeparatorControl
 * 	attributes={props.attributes}
 * 	setAttributes={props.setAttributes}
 * 	clientId={props.clientId}
 * />
 */
export default ( { separator, separatorType, setAttributes } ) => {

	// Get the separator options.
	const separators = getSeparators();

	// Builds a menu item for an separator.
	const separatorButton = ( sep, index ) => (
		<Button
			key={ index }
			isPressed={ separator === sep.value }
			className="x3p0-breadcrumbs-sep-picker__button"
			label={ sep.label }
			showTooltip
			onClick={ () => setAttributes( {
				separator:     sep.value,
				separatorType: sep.type
			} ) }
		>
			{ 'image' === sep.type ? sep.icon : (
				<span className="x3p0-breadcrumbs-sep-picker__button-text">
					{ sep.icon }
				</span>
			) }
		</Button>
	);

	// Builds an separator picker in a 6-column grid.
	const separatorPicker = (
		<BaseControl
			className="x3p0-breadcrumbs-sep-picker"
			label={ __( 'Separator', 'x3p0-ideas' ) }
		>
			<div className="x3p0-breadcrumbs-sep-picker__description">
				{ __( 'Pick an icon or symbol that sits in between and separates breadcrumb items.', 'x3p0-ideas' ) }
			</div>
			<Grid className="x3p0-breadcrumbs-sep-picker__grid" columns="6">
				{ separators.map( ( sep, index ) =>
					separatorButton( sep, index )
				) }
			</Grid>
		</BaseControl>
	);

	// Returns the dropdown menu item.
	return (
		<Dropdown
			className="x3p0-breadcrumbs-sep-dropdown"
			contentClassName="x3p0-breadcrumbs-sep-popover"
			focusOnMount
			popoverProps={ {
				headerTitle: __( 'Separator', 'x3p0-ideas' ),
				variant: 'toolbar'
			} }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<ToolbarButton
					className="x3p0-breadcrumbs-sep-dropdown__button"
					icon={ next }
					label={ __( 'Separator', 'x3p0-ideas' ) }
					onClick={ onToggle }
					aria-expanded={ isOpen }
					isPressed={ !! separator }
				/>
			) }
			renderContent={ () => separatorPicker }
		/>
	);
};
