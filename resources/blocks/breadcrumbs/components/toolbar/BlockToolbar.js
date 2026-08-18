/**
 * Block toolbar (block controls) component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import JustifyControl from './JustifyControl';

// WordPress dependencies.
import { BlockControls, useBlockEditingMode } from '@wordpress/block-editor';

/**
 * Wrapper around the WordPress `<BlockControls>` component for building the
 * block's custom toolbar (block) controls. Renders `false` (nothing) outside
 * the default editing mode — e.g. when the block is content-locked or
 * navigation-only — since those modes hide the block toolbar anyway.
 * @param props
 * @returns {JSX.Element|boolean}
 */
const BlockToolbar = (props) => 'default' === useBlockEditingMode() && (
	<BlockControls group="block">
		<JustifyControl {...props}/>
	</BlockControls>
);

export default BlockToolbar;
