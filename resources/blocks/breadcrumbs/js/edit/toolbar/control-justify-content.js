/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { JustifyContentControl } from '@wordpress/block-editor';

// Exports the breadcrumbs block type edit function.
export default ({ attributes: { justifyContent }, setAttributes }) => (
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
