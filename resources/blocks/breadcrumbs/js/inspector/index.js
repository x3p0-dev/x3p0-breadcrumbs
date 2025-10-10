/**
 * Returns the block inspector controls.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import LabelsPanel from './panel-labels';
import PostTaxonomyPanel from './panel-post-taxonomy';
import RewriteTagsPanel from './panel-rewrite-tags';
import SettingsPanel from './panel-settings';

// WordPress dependencies.
import { InspectorControls } from '@wordpress/block-editor';

// Exports the breadcrumbs block type edit function.
export default (props) => {
	return (
		<InspectorControls group="settings">
			<SettingsPanel {...props}/>
			<LabelsPanel {...props}/>
			<RewriteTagsPanel {...props}/>
			<PostTaxonomyPanel {...props}/>
		</InspectorControls>
	);
};
