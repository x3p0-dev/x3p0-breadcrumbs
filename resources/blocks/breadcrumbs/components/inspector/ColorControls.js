/**
 * Color block inspector controls.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import { __ } from '@wordpress/i18n';

import {
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients
} from '@wordpress/block-editor';

const ColorControls = ({
	attributes: {
		customSeparatorColor
	},
	setAttributes,
	separatorColor,
	setSeparatorColor,
	clientId
}) => {
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
		<>
			<ColorGradientSettingsDropdown
				settings={ [ separatorSettings ] }
				panelId={ clientId }
				__experimentalIsRenderedInSidebar={ true }
				{ ...colorGradientOptions }
			/>
		</>
	);
};

export default ColorControls;
