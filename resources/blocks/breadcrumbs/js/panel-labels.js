/**
 * Labels panel.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { useInstanceId } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

import {
	TextControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';

// Exports the post taxonomy panel.
const LabelsPanel = ({
	attributes,
	setAttributes
}) => {
	const panelId = useInstanceId(LabelsPanel);

	const { labels } = attributes;

	const onLabelChange = (type, value) => {
		const updatedLabels = {
			...labels,
			[type]: value
		};

		// Remove empty values
		if (! value) {
			delete updatedLabels[value];
		}

		setAttributes({ labels: updatedLabels });
	};

	const resetPanelItem = (type) => () => {
		const updatedLabels = { ...labels };
		delete updatedLabels[type];
		setAttributes({ labels: updatedLabels });
	};

	// Resets the post taxonomies to the default.
	const resetPanel = () => setAttributes({
		labels: {}
	});

	return (
		<ToolsPanel
			label={__('Labels', 'x3p0-breadcrumbs')}
			resetAll={ resetPanel }
			panelId={ panelId }
		>
			<ToolsPanelItem
				label={ __('Home', 'x3p0-breadcrumbs') }
				hasValue={ () => !! labels.home }
				onDeselect={ resetPanelItem('home') }
				panelId={ panelId }
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __('Home', 'x3p0-breadcrumbs') }
					placeholder={ __('Home', 'x3p0-breadcrumbs') }
					value={ labels.home }
					onChange={(value) => onLabelChange('home', value)}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Archives', 'x3p0-breadcrumbs') }
				hasValue={ () => !! labels.archives }
				onDeselect={ resetPanelItem('archives') }
				panelId={ panelId }
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __('Archives', 'x3p0-breadcrumbs') }
					placeholder={ __('Archives', 'x3p0-breadcrumbs') }
					value={ labels.archives }
					onChange={(value) => onLabelChange('archives', value)}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('Search Results', 'x3p0-breadcrumbs') }
				hasValue={ () => !! labels.search }
				onDeselect={ resetPanelItem('search') }
				panelId={ panelId }
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __('Search Results', 'x3p0-breadcrumbs') }
					placeholder={ __('Search results for: %s', 'x3p0-breadcrumbs') }
					value={ labels.search }
					onChange={(value) => onLabelChange('search', value)}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={ __('404 Not Found', 'x3p0-breadcrumbs') }
				hasValue={ () => !! labels.error_404 }
				onDeselect={ resetPanelItem('error_404') }
				panelId={ panelId }
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __('404 Not Found', 'x3p0-breadcrumbs') }
					placeholder={ __('404 Not Found', 'x3p0-breadcrumbs') }
					value={ labels.error_404 }
					onChange={(value) => onLabelChange('error_404', value)}
				/>
			</ToolsPanelItem>
		</ToolsPanel>
	);
};

export default LabelsPanel;
