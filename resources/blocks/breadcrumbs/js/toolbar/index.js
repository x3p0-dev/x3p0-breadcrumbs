/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import HomePrefixControl  from './control-home-prefix';
import SeparatorControl from './control-separator';

import {
	BlockControls,
	JustifyContentControl
} from '@wordpress/block-editor';

// Define allowed justification controls.
const justifyOptions = [ 'left', 'center', 'right' ];

// Exports the breadcrumbs block type edit function.
export default (props) => {

	const {
		attributes,
		setAttributes,
	} = props;

	const { justifyContent } = attributes;

	const blockToolbarControls = (
		<BlockControls group="block">
			<JustifyContentControl
				allowedControls={ justifyOptions }
				value={ justifyContent }
				onChange={ (value) => setAttributes({
					justifyContent: value
				}) }
				popoverProps={ {
					position: 'bottom right',
					variant: 'toolbar'
				} }
			/>
		</BlockControls>
	);

	const otherToolbarControls = (
		<BlockControls group="other">
			<HomePrefixControl {...props}/>
			<SeparatorControl {...props}/>
		</BlockControls>
	);

	return (
		<>
			{ blockToolbarControls }
			{ otherToolbarControls }
		</>
	);
};
