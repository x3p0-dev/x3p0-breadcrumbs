/**
 * Labels panel component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
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
	const { ariaLabel, iconVisibility, labels = {}, labelVisibility, showTrailStart } = attributes;

	// A coarse approximation of `Html::isCrumbLabelHidden()` per trail
	// position: a label is only ever actually hidden when its icon renders
	// in its place, so both visibility settings matter. The home crumb is
	// always first in the trail; the search/404 crumbs are the trail's
	// current (last) item. (Single-crumb-trail edge cases are ignored.)
	const hiddenLabelHelp = __('Label is visually hidden but is readable to users with assistive technology.', 'x3p0-breadcrumbs');

	const firstIconVisible = ['all', 'all-but-last', 'first'].includes(iconVisibility);
	const lastIconVisible  = 'all' === iconVisibility;

	const homeLabelHidden = firstIconVisible && 'all' !== labelVisibility;
	const lastLabelHidden = lastIconVisible && 'none' === labelVisibility;

	const onLabelChange = (type, value) => {
		const updatedLabels = {...labels};

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
			showTrailStart ? [{
				name: 'home',
				label: __('Home', 'x3p0-breadcrumbs'),
				help: homeLabelHidden ? hiddenLabelHelp : ''
			}] : []
		),
		{
			name: 'search',
			label: __('Search Results', 'x3p0-breadcrumbs'),
			placeholder: __('Search results for: %s', 'x3p0-breadcrumbs'),
			help: lastLabelHidden ? hiddenLabelHelp : ''
		},
		{
			name: 'error_404',
			label: __('404 Not Found', 'x3p0-breadcrumbs'),
			placeholder: __('Page not found', 'x3p0-breadcrumbs'),
			help: lastLabelHidden ? hiddenLabelHelp : ''
		}
	];

	return (
		<ToolsPanel
			label={__('Labels', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({ ariaLabel: undefined, labels: undefined })}
			panelId={panelId}
		>
			{labelSettings.map((item) => (
				<ToolsPanelItem
					key={`x3p0-breadcrumbs-labels-${item.name}`}
					label={item.label}
					hasValue={() => !!labels[item.name]}
					onDeselect={resetPanelItem(item.name)}
					panelId={panelId}
					isShownByDefault
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
			<ToolsPanelItem
				label={__('ARIA Label', 'x3p0-breadcrumbs')}
				hasValue={() => !!ariaLabel}
				onDeselect={() => setAttributes({ ariaLabel: undefined })}
				panelId={panelId}
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('ARIA Label', 'x3p0-breadcrumbs')}
					placeholder={__('Breadcrumbs', 'x3p0-breadcrumbs')}
					value={ariaLabel || ''}
					onChange={(value) => setAttributes({ ariaLabel: value || undefined })}
					help={__('Describes the breadcrumb trail for screen reader users. Leave blank to use the default label.', 'x3p0-breadcrumbs')}
				/>
			</ToolsPanelItem>
		</ToolsPanel>
	);
};

export default LabelsPanel;
