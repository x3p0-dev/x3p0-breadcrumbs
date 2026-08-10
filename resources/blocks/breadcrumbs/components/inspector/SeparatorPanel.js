/**
 * Separator panel component.
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
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients
} from '@wordpress/block-editor';

import {
	__experimentalToolsPanel as ToolsPanel
} from '@wordpress/components';

/**
 * Renders a `<ToolsPanel>` component with the block's label controls.
 * @param props
 * @returns {JSX.Element}
 */
const SeparatorPanel = ({
	attributes: {
		customSeparatorColor
	},
	setAttributes,
	separatorColor,
	setSeparatorColor
}) => {
	const panelId = useInstanceId(SeparatorPanel);

	// Get the base color and gradient options to pass into individual color
	// settings for our Color panel.
	const colorGradientOptions = useMultipleOriginColorsAndGradients();

	const separatorSettings = {
		label: __('Separator', 'x3p0-breadcrumbs'),
		colorValue: separatorColor.color || customSeparatorColor,
		onColorChange: (value) => {
			setSeparatorColor(value);
			setAttributes({ customSeparatorColor: value });
		},
		resetAllFilter: () => {
			setSeparatorColor(undefined);
			setAttributes({ customSeparatorColor: undefined });
		},
		clearable: true,
		enableAlpha: true,
		hasColorsOrGradients: false,
		isShownByDefault: true
	};

	return (
		<ToolsPanel
			label={__('Separator', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({ labels: undefined })}
			panelId={panelId}
		>
			<ColorGradientSettingsDropdown
				settings={ [ separatorSettings ] }
				panelId={ panelId }
				__experimentalIsRenderedInSidebar={ true }
				{ ...colorGradientOptions }
			/>
		</ToolsPanel>
	);
};

export default SeparatorPanel;
