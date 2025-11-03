/**
 * Labels panel component.
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
	TextControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';

/**
 * Renders a `<ToolsPanel>` component with the block's label controls.
 * @param props
 * @returns {JSX.Element}
 */
const LabelsPanel = ({ attributes, setAttributes }) => {
	const panelId = useInstanceId(LabelsPanel);
	const { labels = {}, showHomeLabel, showTrailStart } = attributes;

	const onLabelChange = (type, value) => {
		const updatedLabels = { ...labels };

		if (value) {
			updatedLabels[type] = value;
		} else {
			delete updatedLabels[type];
		}

		setAttributes({ labels: updatedLabels });
	};

	const resetPanelItem = (type) => () => onLabelChange(type, '');

	const labelSettings = [
		...(
			showTrailStart
			? [
				{
					name: 'home',
					label: __('Home', 'x3p0-breadcrumbs'),
					help: ! showHomeLabel
						? __('Label is visually hidden but is readable to users with assistive technology.', 'x3p0-breadcrumbs')
						: ''
				}
			]
			: []
		),
		{
			name: 'search',
			label: __('Search Results', 'x3p0-breadcrumbs'),
			placeholder: __('Search results for: %s', 'x3p0-breadcrumbs')
		},
		{
			name: 'error_404',
			label: __('404 Not Found', 'x3p0-breadcrumbs')
		}
	];

	return (
		<ToolsPanel
			label={__('Labels', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({ labels: undefined })}
			panelId={panelId}
		>
			{labelSettings.map((item) => (
				<ToolsPanelItem
					key={`x3p0-breadcrumbs-labels-${item.name}`}
					label={item.label}
					hasValue={() => !!labels[item.name]}
					onDeselect={resetPanelItem(item.name)}
					panelId={panelId}
				>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={item.label}
						placeholder={item.placeholder || item.label}
						value={labels[item.name] || ''}
						onChange={(value) => onLabelChange(item.name, value)}
						help={item.help || ''}
					/>
				</ToolsPanelItem>
			))}
		</ToolsPanel>
	);
};

export default LabelsPanel;
