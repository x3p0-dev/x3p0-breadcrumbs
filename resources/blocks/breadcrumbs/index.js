/**
 * Registers the breadcrumbs toolbar.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Import stylesheets.
import './scss/index.scss';
import './scss/style.scss';

// Import dependencies.
import { registerBlockType } from '@wordpress/blocks';

// Import the block data and components.
import metadata   from './block.json';
import edit       from './edit';
import icon       from './icon';
import deprecated from './deprecated';
import transforms from './transforms';

// Keep the accepted markup values in sync with the server-defined list
// (`MarkupType`), passed in on the `x3p0Breadcrumbs` global.
//
// noinspection JSUnresolvedVariable
metadata.attributes.markup.enum =
	(window.x3p0Breadcrumbs?.markupTypes ?? []).map(({key}) => key);

// noinspection JSUnresolvedVariable
if (window.x3p0Breadcrumbs?.defaultMarkup) {
	// noinspection JSUnresolvedVariable
	metadata.attributes.markup.default = window.x3p0Breadcrumbs.defaultMarkup;
}

// Register the block type.
registerBlockType(metadata, { edit, icon, deprecated, transforms });
