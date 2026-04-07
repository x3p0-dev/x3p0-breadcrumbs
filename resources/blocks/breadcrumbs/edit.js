/**
 * Block edit.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import BlockToolbar   from './components/toolbar/BlockToolbar';
import BlockInspector from './components/inspector/BlockInspector';
import BlockContent   from './components/content/BlockContent';
import {withColors}   from '@wordpress/block-editor';

/**
 * Renders the block edit component.
 * @param props
 * @returns {JSX.Element}
 */
export default withColors({
	separatorColor: 'separator-color'
})((props) => (
	<>
		<BlockToolbar {...props}/>
		<BlockInspector {...props}/>
		<BlockContent {...props}/>
	</>
));
