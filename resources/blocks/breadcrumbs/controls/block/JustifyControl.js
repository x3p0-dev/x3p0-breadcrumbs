/**
 * Justify control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { JustifyContentControl } from '@wordpress/block-editor';

/**
 * Renders the content justification control.
 * @param props
 * @returns {JSX.Element}
 */
const JustifyControl = ({ attributes: { justifyContent }, setAttributes }) => (
	<JustifyContentControl
		allowedControls={ [ 'left', 'center', 'right' ] }
		value={ justifyContent }
		onChange={ (value) => setAttributes({
			justifyContent: value
		}) }
		popoverProps={ {
			position: 'bottom right',
			variant: 'toolbar'
		} }
	/>
);

export default JustifyControl;
