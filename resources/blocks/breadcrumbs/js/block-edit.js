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
import SettingsPanel from './panel-settings';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';

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
		showHomeLabel,
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

	const settingsControls = (
		<InspectorControls group="settings">
			<SettingsPanel
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
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

	const homeLabel = (
		<RichText
			tagName="span"
			className="breadcrumbs__crumb-label"
			aria-label={ __('Home breadcrumb label', 'x3p0-breadcrumbs') }
			placeholder={ __('Home', 'x3p0-breadcrumbs') }
			value={ labels?.home }
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
	);

	// Builds a preview breadcrumb trail for the editor.
	const trail = (
		<ol className="breadcrumbs__trail">
			{ showTrailStart && (
				<li className="breadcrumbs__crumb breadcrumbs__crumb--home">
					<a
						href="#breadcrumbs-pseudo-link"
						onClick={ preventDefault }
						className="breadcrumbs__crumb-content"
					>
						{ homeLabel }
					</a>
				</li>
			)}
			<li className="breadcrumbs__crumb breadcrumbs__crumb--post">
				<a
					href="#breadcrumbs-pseudo-link"
					onClick={ preventDefault }
					className="breadcrumbs__crumb-content"
				>
					<span className="breadcrumbs__crumb-label">
						{ __('Parent Page', 'x3p0-breadcrumbs') }
					</span>
				</a>
			</li>
			{ showTrailEnd && (
				<li className="breadcrumbs__crumb breadcrumbs__crumb--post">
					<a className="breadcrumbs__crumb-content">
						<span className="breadcrumbs__crumb-label">
							{ __('Current Page', 'x3p0-breadcrumbs') }
						</span>
					</a>
				</li>
			)}
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
