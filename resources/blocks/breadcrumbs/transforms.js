/**
 * Block transforms.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Import dependencies.
import {createBlock} from '@wordpress/blocks';

/**
 * Transforms the core breadcrumbs block into this plugin's block. Only the
 * attributes with a faithful mapping are carried over; core's `separator`
 * (a literal glyph vs. this block's icon keys) and `prefersTaxonomy` (a global
 * boolean vs. this block's per-post-type `postTaxonomy`/`mapRewriteTags`) have
 * no reliable equivalent, so they fall back to this block's defaults.
 */
export default {
	from: [
		{
			type: 'block',
			blocks: ['core/breadcrumbs'],
			transform: ({showOnHomePage, showCurrentItem, showHomeItem}) =>
				createBlock('x3p0/breadcrumbs', {
					showOnHomepage: showOnHomePage,
					showTrailEnd: showCurrentItem,
					showTrailStart: showHomeItem
				})
		}
	]
};
