/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-3.0-or-later
 */

// Internal dependencies.
import HomePrefixControl  from './control-home-prefix';
import SeparatorControl from './control-separator';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { CustomSelectControl, PanelBody, ToggleControl } from '@wordpress/components';

import {
	BlockControls,
	InspectorControls,
	JustifyContentControl,
	useBlockProps
} from '@wordpress/block-editor';

// Third-party dependencies.
import classnames from 'classnames';

// Prevent breadcrumb link events when users click them.
const preventDefault = (event) => event.preventDefault();

// Define allowed justification controls.
const justifyOptions = [ 'left', 'center', 'right' ];

// Define the markup options.
const markupOptions = [
	{
		key: 'html',
		name: __('Plain HTML', 'x3p0-breadcrumbs')
	},
	{
		key: 'microdata',
		name: __('Microdata (Schema.org)', 'x3p0-breadcrumbs')
	},
	{
		key: 'rdfa',
		name: __('RDFa (Schema.org)', 'x3p0-breadcrumbs')
	}
];

// Exports the breadcrumbs block type edit function.
export default ({
	attributes: {
		homePrefix,
		homePrefixType,
		justifyContent,
		markup,
		showHomeLabel,
		showOnHomepage,
		showTrailStart,
		showTrailEnd,
		separator,
		separatorType
	},
	setAttributes
}) => {
	// =====================================================================
	// Build the block toolbar controls.
	// =====================================================================

	const blockToolbarControls = (
		<BlockControls group="block">
			<JustifyContentControl
				allowedControls={ justifyOptions }
				value={ justifyContent }
				onChange={ (value) => setAttributes({
					justifyContent: value
				}) }
				popoverProps={ {
					position: 'bottom right',
					variant: 'toolbar'
				} }
			/>
		</BlockControls>
	);

	const otherToolbarControls = (
		<BlockControls group="other">
			<HomePrefixControl
				homePrefix={ homePrefix }
				showHomeLabel={ showHomeLabel }
				showTrailStart={ showTrailStart }
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
			label={ __('Show on homepage', 'x3p0-breadcrumbs') }
			help={
				showOnHomepage
				? __('Breadcrumbs display on the homepage.', 'x3p0-breadcrumbs')
				: __('Breadcrumbs hidden on the homepage.', 'x3p0-breadcrumbs')
			}
			checked={ showOnHomepage }
			onChange={ () => setAttributes({
				showOnHomepage: ! showOnHomepage
			}) }
		/>
	);

	const showTrailStartControl = (
		<ToggleControl
			label={ __('Show first breadcrumb', 'x3p0-breadcrumbs') }
			help={
				showTrailStart
					? __('First breadcrumb item is shown.',  'x3p0-breadcrumbs')
					: __('First breadcrumb item is hidden.', 'x3p0-breadcrumbs')
			}
			checked={ showTrailStart }
			onChange={ () => setAttributes({
				homePrefix:     '',
				homePrefixType: '',
				showHomeLabel:  true,
				showTrailStart: ! showTrailStart
			}) }
		/>
	);

	const showTrailEndControl = (
		<ToggleControl
			label={ __('Show last breadcrumb', 'x3p0-breadcrumbs') }
			help={
				showTrailEnd
				? __('Last breadcrumb item is shown.',  'x3p0-breadcrumbs')
				: __('Last breadcrumb item is hidden.', 'x3p0-breadcrumbs')
			}
			checked={ showTrailEnd }
			onChange={ () => setAttributes({
				showTrailEnd: ! showTrailEnd
			}) }
		/>
	);

	const markupControl = (
		<CustomSelectControl
			label={ __('Markup style', 'x3p0-breadcrumbs') }
			options={ markupOptions }
			value={ markupOptions.find(
				(option) => option.key === markup
			)}
			onChange={ ({ selectedItem }) => setAttributes({
				markup: selectedItem.key
			})}
		/>
	);

	const settingsControls = (
		<InspectorControls group="settings">
			<PanelBody title={
				__('Breadcrumb settings', 'x3p0-breadcrumbs')
			}>
				{ showOnHomepageControl }
				{ showTrailStartControl }
				{ showTrailEndControl }
				{ markupControl }
			</PanelBody>
		</InspectorControls>
	);

	// =====================================================================
	// Build the block output for the content canvas.
	// =====================================================================

	// Get the blockProps and add custom classes.
	const blockProps = useBlockProps({
		className: classnames({
			'breadcrumbs': true,
			[ `has-home-${homePrefixType}-${ homePrefix }`   ] : showTrailStart && homePrefixType && homePrefix,
			[ 'hide-home-label'                              ] : showTrailStart && ! showHomeLabel,
			[ `has-sep-${separatorType}-${ separator }`      ] : separatorType && separator,
			[ `is-content-justification-${ justifyContent }` ] : justifyContent
		})
	});

	// Build an array of faux breadcrumb items to show.
	let crumbs = [
		{
			type: 'home',
			label: __('Home', 'x3p0-breadcrumbs'),
			link: true
		},
		{
			type: 'post',
			label: __('Parent Page', 'x3p0-breadcrumbs'),
			link: true
		},
		{
			type: 'post',
			label: __('Current Page', 'x3p0-breadcrumbs'),
			link: false
		}
	];

	// Remove first item if trail start isn't shown.
	if (! showTrailStart) {
		crumbs.shift();
	}

	// Remove last item if trail end isn't shown.
	if (! showTrailEnd) {
		crumbs.pop();
	}

	// Creates a breadcrumb trail list item.
	const crumb = (crumb, index) => {
		const CrumbContent = crumb.link ? 'a' : 'span';

		return (
			<li
				key={ index }
				className={ `breadcrumbs__crumb breadcrumbs__crumb--${ crumb.type }` }
			>
				<CrumbContent
					href={ crumb.link ? '#breadcrumbs-pseudo-link' : null }
					onClick={ preventDefault }
					className="breadcrumbs__crumb-content"
				>
					<span className="breadcrumbs__crumb-label">
						{ crumb.label }
					</span>
				</CrumbContent>
			</li>
		)
	};

	// Builds a preview breadcrumb trail for the editor.
	const trail = (
		<ol className="breadcrumbs__trail">
			{ crumbs.map((item, index) => crumb(item, index)) }
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
