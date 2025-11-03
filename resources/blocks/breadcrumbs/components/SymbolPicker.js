/**
 * Symbol picker component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { BaseControl, Button, __experimentalGrid as Grid } from '@wordpress/components';

/**
 * Wrapping around the WordPress `<Grid>` component for rendering a grid-based
 * symbol/icon picker.
 * @param props
 * @returns {JSX.Element}
 */
export const SymbolPicker = ({
	value,
	onChange,
	options,
	label,
	description
}) => (
	<BaseControl
		className="x3p0-breadcrumbs-symbol-picker"
		label={label}
		__nextHasNoMarginBottom
	>
		<div className="x3p0-breadcrumbs-symbol-picker__description">
			{description}
		</div>
		<Grid className="x3p0-breadcrumbs-symbol-picker__grid" columns="6">
			{options.map((symbol, index) => (
				<Button
					key={index}
					isPressed={value === symbol.value}
					className="x3p0-breadcrumbs-symbol-picker__button"
					label={symbol.label}
					showTooltip
					onClick={() => onChange(symbol.value)}
				>
					<span className="x3p0-breadcrumbs-symbol-picker__button-text">
						{symbol.icon}
					</span>
				</Button>
			))}
		</Grid>
	</BaseControl>
);
