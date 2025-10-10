/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import JustifyContent from './control-justify-content';
import HomePrefix  from './control-home-prefix';
import Separator from './control-separator';

// WordPress dependencies.
import { BlockControls } from '@wordpress/block-editor';

// Exports the breadcrumbs block type edit function.
export default (props) => (
	<>
		<BlockControls group="block">
			<JustifyContent {...props}/>
		</BlockControls>
		<BlockControls group="other">
			<HomePrefix {...props}/>
			<Separator {...props}/>
		</BlockControls>
	</>
);
