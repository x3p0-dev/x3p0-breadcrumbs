/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import HomePrefixControl  from './control-home-prefix';
import SeparatorControl from './control-separator';
import LabelsPanel from './panel-labels';
import PostTaxonomyPanel from './panel-post-taxonomy';
import RewriteTagsPanel from './panel-rewrite-tags';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { CustomSelectControl, PanelBody, ToggleControl } from '@wordpress/components';

import {
	BlockControls,
	InspectorControls,
	JustifyContentControl,
	RichText,
	useBlockProps,
	useInnerBlocksProps
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
	attributes,
	setAttributes
}) => {
	const {
		homePrefix,
		homePrefixType,
		justifyContent,
		labels,
		markup,
		showHomeLabel,
		showOnHomepage,
		showTrailStart,
		showTrailEnd,
		separator,
		separatorType
	} = attributes;

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
			__next40pxDefaultSize={true}
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
			<LabelsPanel
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<RewriteTagsPanel
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<PostTaxonomyPanel
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
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

	// Need inner block props for layout styles to work properly in the admin.
	const innerBlockProps = useInnerBlocksProps(blockProps);

	// Build an array of faux breadcrumb items to show.
	let crumbs = [
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
			<li
				key="wp-block-x3p0-breadcrumbs-home"
				className="breadcrumbs__crumb breadcrumbs__crumb--home"
			>
				<a
					href="#breadcrumbs-pseudo-link"
					onClick={ preventDefault }
					className="breadcrumbs__crumb-content"
				>
					<RichText
						tagName="span"
						className="breadcrumbs__crumb-label"
						aria-label={ __('Home breadcrumb label', 'x3p0-breadcrumbs') }
						placeholder={ __('Home', 'x3p0-breadcrumbs') }
						value={ labels.home }
						multiline={ false }
						onChange={ (value) => {
							const updatedLabels = {
								...labels,
								home: value
							};

							// Remove empty values
							if (! value) {
								delete updatedLabels.home;
							}

							setAttributes({ labels: updatedLabels });
						}}
						withoutInteractiveFormatting={ true }
					/>
				</a>
			</li>
			{ crumbs.map((item, index) => crumb(item, index)) }
		</ol>
	);

	// Return the final block edit component.
	return (
		<>
			{ toolbarControls }
			{ settingsControls }
			<nav { ...innerBlockProps }>
				{ trail }
			</nav>
		</>
	);
};
