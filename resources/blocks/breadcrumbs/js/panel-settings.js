/**
 * Post taxonomies panel.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { useInstanceId } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	CustomSelectControl,
	ToggleControl
} from '@wordpress/components';

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

// Exports the post taxonomy panel.
const SettingsPanel = ({ attributes, setAttributes }) => {
	const panelId = useInstanceId(SettingsPanel);

	const {
		markup,
		showOnHomepage,
		showTrailStart,
		showTrailEnd
	} = attributes;

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

	const showOnHomepageControl = (
		<ToggleControl
			label={ __('Show on homepage', 'x3p0-breadcrumbs') }
			checked={ showOnHomepage }
			onChange={ () => setAttributes({
				showOnHomepage: ! showOnHomepage
			}) }
			__nextHasNoMarginBottom={ true }
		/>
	);

	const showTrailStartControl = (
		<ToggleControl
			label={ __('Show first breadcrumb', 'x3p0-breadcrumbs') }
			checked={ showTrailStart }
			onChange={ () => setAttributes({
				homePrefix:     '',
				homePrefixType: '',
				showHomeLabel:  true,
				showTrailStart: ! showTrailStart
			}) }
			__nextHasNoMarginBottom={ true }
		/>
	);

	const showTrailEndControl = (
		<ToggleControl
			label={ __('Show last breadcrumb', 'x3p0-breadcrumbs') }
			checked={ showTrailEnd }
			onChange={ () => setAttributes({
				showTrailEnd: ! showTrailEnd
			}) }
			__nextHasNoMarginBottom={ true }
		/>
	);

	const resetMarkup = () => setAttributes({ markup: 'rdfa' });
	const resetShowOnHomepage = () => setAttributes({ showOnHomepage: false });
	const resetShowTrailStart = () => setAttributes({ showTrailStart: true });
	const resetShowTrailEnd = () => setAttributes({ showTrailEnd: true });

	const resetPanel = () => {
		resetMarkup();
		resetShowOnHomepage();
		resetShowTrailStart();
		resetShowTrailEnd();
	};

	return (
		<ToolsPanel
			label={__('Settings', 'x3p0-breadcrumbs')}
			resetAll={ resetPanel }
			panelId={ panelId }
		>
			<ToolsPanelItem
				label={ __('Markup Style', 'x3p0-breadcrumbs') }
				hasValue={ () => !! markup }
				onDeselect={ resetMarkup }
				panelId={ panelId }
				isShownByDefault={ true }
			>
				{ markupControl }
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Show on homepage', 'x3p0-breadcrumbs') }
				hasValue={ () => !! showOnHomepage }
				onDeselect={ resetShowOnHomepage }
				panelId={ panelId }
				isShownByDefault={ true }
			>
				{ showOnHomepageControl }
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Show first breadcrumb', 'x3p0-breadcrumbs') }
				hasValue={ () => !! showTrailStart }
				onDeselect={ resetShowTrailStart }
				panelId={ panelId }
			>
				{ showTrailStartControl }
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Show last breadcrumb', 'x3p0-breadcrumbs') }
				hasValue={ () => !! showTrailEnd }
				onDeselect={ resetShowTrailEnd }
				panelId={ panelId }
			>
				{ showTrailEndControl }
			</ToolsPanelItem>
		</ToolsPanel>
	);
};

export default SettingsPanel;
