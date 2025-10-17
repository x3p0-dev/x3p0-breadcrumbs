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
const MARKUP_OPTIONS = [
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
const SettingsPanel = ({
	attributes: {
		linkTrailEnd,
		markup,
		showOnHomepage,
		showTrailStart,
		showTrailEnd
	},
	setAttributes
}) => {
	const panelId = useInstanceId(SettingsPanel);

	return (
		<ToolsPanel
			label={__('Settings', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({
				markup: 'rdfa',
				showOnHomepage: false,
				showTrailStart: false,
				showTrailEnd: false,
				linkTrailEnd: false
			})}
			panelId={ panelId }
		>
			<ToolsPanelItem
				label={ __('Markup style', 'x3p0-breadcrumbs') }
				hasValue={ () => markup !== 'rdfa' }
				onDeselect={() => setAttributes({ markup: 'rdfa' })}
				panelId={ panelId }
				isShownByDefault={ true }
			>
				<CustomSelectControl
					label={ __('Markup style', 'x3p0-breadcrumbs') }
					options={ MARKUP_OPTIONS }
					value={ MARKUP_OPTIONS.find(
						(option) => option.key === markup
					)}
					onChange={ ({ selectedItem }) => setAttributes({
						markup: selectedItem.key
					})}
					__next40pxDefaultSize={true}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Show on homepage', 'x3p0-breadcrumbs') }
				hasValue={ () => !! showOnHomepage }
				onDeselect={() => setAttributes({ showOnHomepage: false })}
				panelId={ panelId }
				isShownByDefault={ true }
			>
				<ToggleControl
					label={ __('Show on homepage', 'x3p0-breadcrumbs') }
					checked={ showOnHomepage }
					onChange={ () => setAttributes({
						showOnHomepage: ! showOnHomepage
					}) }
					__nextHasNoMarginBottom={ true }
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Show first breadcrumb', 'x3p0-breadcrumbs') }
				hasValue={ () => !! showTrailStart }
				onDeselect={() => setAttributes({ showTrailStart: false })}
				panelId={ panelId }
			>
				<ToggleControl
					label={ __('Show first breadcrumb', 'x3p0-breadcrumbs') }
					checked={ showTrailStart }
					onChange={ () => setAttributes({
						homeIcon:       '',
						showHomeLabel:  true,
						showTrailStart: ! showTrailStart
					}) }
					__nextHasNoMarginBottom={ true }
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Show last breadcrumb', 'x3p0-breadcrumbs') }
				hasValue={ () => !! showTrailEnd }
				onDeselect={() => setAttributes({ showTrailEnd: false })}
				panelId={ panelId }
			>
				<ToggleControl
					label={ __('Show last breadcrumb', 'x3p0-breadcrumbs') }
					checked={ showTrailEnd }
					onChange={ () => setAttributes({
						showTrailEnd: ! showTrailEnd
					}) }
					__nextHasNoMarginBottom={ true }
				/>
			</ToolsPanelItem>
			{showTrailEnd && (
				<ToolsPanelItem
					label={ __('Link last breadcrumb', 'x3p0-breadcrumbs') }
					hasValue={ () => !! linkTrailEnd }
					onDeselect={() => setAttributes({
						linkTrailEnd: false
					})}
					panelId={ panelId }
				>
					<ToggleControl
						label={ __('Link last breadcrumb', 'x3p0-breadcrumbs') }
						checked={ linkTrailEnd }
						onChange={ () => setAttributes({
							linkTrailEnd: ! linkTrailEnd
						}) }
						__nextHasNoMarginBottom={ true }
					/>
				</ToolsPanelItem>
			)}
		</ToolsPanel>
	);
};

export default SettingsPanel;
