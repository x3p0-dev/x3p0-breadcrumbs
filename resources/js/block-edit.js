/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-2.0-or-later
 */

// Internal dependencies.
import HomeIconControl  from './control-home-prefix';
import SeparatorControl from './control-separator';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { PanelBody, ToggleControl } from '@wordpress/components';

import {
	BlockControls,
	InspectorControls,
	JustifyContentControl,
	useBlockProps
} from '@wordpress/block-editor';

// Third-party dependencies.
import classnames from 'classnames';

// Prevent breadcrumb link events when users click them.
const preventDefault = ( event ) => event.preventDefault();

// Define allowed justification controls.
const justifyOptions = [ 'left', 'center', 'right' ];

// Exports the breadcrumbs block type edit function.
export default ( {
	attributes: {
		homePrefix,
		homePrefixType,
		justifyContent,
		showHomeLabel,
		showOnHomepage,
		showTrailEnd,
		separator,
		separatorType
	},
	setAttributes
} ) => {
	// =====================================================================
	// Build the block toolbar controls.
	// =====================================================================

	const blockToolbarControls = (
		<BlockControls group="block">
			<JustifyContentControl
				allowedControls={ justifyOptions }
				value={ justifyContent }
				onChange={ ( value ) => setAttributes( {
					justifyContent: value
				} ) }
				popoverProps={ {
					position: 'bottom right',
					variant: 'toolbar'
				} }
			/>
		</BlockControls>
	);

	const otherToolbarControls = (
		<BlockControls group="other">
			<HomeIconControl
				homePrefix={ homePrefix }
				showHomeLabel={ showHomeLabel }
				setAttributes={ setAttributes }
			/>
			<SeparatorControl
				separator={ separator }
				setAttributes={ setAttributes }
			/>
		</BlockControls>
	);

	const toolbarControls = (
		<>
			{ blockToolbarControls }
			{ otherToolbarControls }
		</>
	);

	// =====================================================================
	// Build the block inspector sidebar controls.
	// =====================================================================

	const showOnHomepageControl = (
		<ToggleControl
			label={ __( 'Show on homepage', 'x3p0-breadcrumbs' ) }
			help={
				showOnHomepage
				? __( 'Breadcrumbs display on the homepage.', 'x3p0-breadcrumbs' )
				: __( 'Breadcrumbs hidden on the homepage.', 'x3p0-breadcrumbs' )
			}
			checked={ showOnHomepage }
			onChange={ () => setAttributes( {
				showOnHomepage: ! showOnHomepage
			} ) }
		/>
	);

	const showTrailEndControl = (
		<ToggleControl
			label={ __( 'Show last breadcrumb', 'x3p0-breadcrumbs' ) }
			help={
				showTrailEnd
				? __( 'Current page item is shown.', 'x3p0-breadcrumbs' )
				: __( 'Current page item is hidden.', 'x3p0-breadcrumbs' )
			}
			checked={ showTrailEnd }
			onChange={ () => setAttributes( {
				showTrailEnd: ! showTrailEnd
			} ) }
		/>
	);

	const settingsControls = (
		<InspectorControls group="settings">
			<PanelBody title={
				__( 'Breadcrumb settings', 'x3p0-breadcrumbs' )
			}>
				{ showOnHomepageControl }
				{ showTrailEndControl }
			</PanelBody>
		</InspectorControls>
	);

	// =====================================================================
	// Build the block output for the content canvas.
	// =====================================================================

	// Get the blockProps and add custom classes.
	const blockProps = useBlockProps( {
		className: classnames( {
			'breadcrumbs': true,
			[ `has-home-${homePrefixType}-${ homePrefix }`   ] : homePrefixType && homePrefix,
			[ `has-sep-${separatorType}-${ separator }`      ] : separatorType && separator,
			[ `is-content-justification-${ justifyContent }` ] : justifyContent
		} )
	} );

	// Build an array of faux breadcrumb items to show.
	let crumbs = [
		{
			type: 'home',
			label: __( 'Home', 'x3p0-breadcrumbs' ),
			link: true,
			hide: ! showHomeLabel
		},
		{
			type: 'post',
			label: __( 'Parent Page', 'x3p0-breadcrumbs' ),
			link: true,
			hide: false
		}
	];

	if ( showTrailEnd ) {
		crumbs.push( {
			type: 'post',
			label: __( 'Current Page', 'x3p0-breadcrumbs' ),
			link: false,
			hide: false
		} );
	}

	// Creates a breadcrumb trail list item.
	const crumb = ( crumb, index ) => {
		const CrumbContent = crumb.link ? 'a' : 'span';

		return (
			<li
				key={ index }
				className={ `breadcrumbs__crumb breadcrumbs__crumb--${ crumb.type }` }
				itemProp="itemListElement"
				itemScope
				itemType="https://schema.org/ListItem"
			>
				<CrumbContent
					href={ crumb.link ? '#breadcrumbs-pseudo-link' : null }
					onClick={ preventDefault }
					className="breadcrumbs__crumb-content"
					itemProp="item"
				>
					<span
						class={ `breadcrumbs__crumb-label ${ crumb.hide ? 'screen-reader-text' : '' }` }
						itemProp="name"
					>
						{ crumb.label }
					</span>
				</CrumbContent>
				<meta itemProp="position" content={ index + 1 } />
			</li>
		)
	};

	// Builds a preview breadcrumb trail for the editor.
	const trail = (
		<ol className="breadcrumbs__trail" itemScope="" itemType="https://schema.org/BreadcrumbList">
			{ crumbs.map( ( item, index ) => crumb( item, index ) ) }
		</ol>
	);

	// Return the final block edit component.
	return (
		<>
			{ toolbarControls }
			{ settingsControls }
			<nav { ...blockProps }>
				{ trail }
			</nav>
		</>
	);
};
