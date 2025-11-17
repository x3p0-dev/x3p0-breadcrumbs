/**
 * Block toolbar (block controls) component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import JustifyControl   from './JustifyControl';
import HomeControl      from './HomeControl';
import SeparatorControl from './SeparatorControl';

// WordPress dependencies.
import { BlockControls, useBlockEditingMode } from '@wordpress/block-editor';

/**
 * Wrapper around the WordPress `<BlockControls>` component for building the
 * block's custom toolbar (block) controls.
 * @param props
 * @returns {JSX.Element}
 */
const BlockToolbar = (props) => 'default' === useBlockEditingMode() && (
	<>
		<BlockControls group="block">
			<JustifyControl {...props}/>
		</BlockControls>
		<BlockControls group="other">
			<HomeControl {...props}/>
			<SeparatorControl {...props}/>
		</BlockControls>
	</>
);

export default BlockToolbar;
