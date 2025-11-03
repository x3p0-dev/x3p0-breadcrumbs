/**
 * Returns the block inspector controls.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import LabelsPanel       from './LabelsPanel';
import PostTaxonomyPanel from './PostTaxonomyPanel';
import RewriteTagsPanel  from './RewriteTagsPanel';
import SettingsPanel     from './SettingsPanel';

// WordPress dependencies.
import { InspectorControls } from '@wordpress/block-editor';

// Exports the breadcrumbs block type edit function.
const BreadcrumbsInspectorControls = (props) => (
	<InspectorControls group="settings">
		<SettingsPanel {...props}/>
		<LabelsPanel {...props}/>
		<RewriteTagsPanel {...props}/>
		<PostTaxonomyPanel {...props}/>
	</InspectorControls>
);

export default BreadcrumbsInspectorControls;
