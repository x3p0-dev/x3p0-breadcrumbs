/**
 * Returns the block controls.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import JustifyContentControl from './JustifyContentControl';
import HomeIconControl       from './HomeIconControl';
import SeparatorIconControl  from './SeparatorIconControl';

// WordPress dependencies.
import { BlockControls, useBlockEditingMode } from '@wordpress/block-editor';

// Exports the breadcrumbs block type edit function.
const BreadcrumbsBlockControls = (props) => 'default' === useBlockEditingMode() && (
	<>
		<BlockControls group="block">
			<JustifyContentControl {...props}/>
		</BlockControls>
		<BlockControls group="other">
			<HomeIconControl {...props}/>
			<SeparatorIconControl {...props}/>
		</BlockControls>
	</>
);

export default BreadcrumbsBlockControls;
