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
 * Transforms other breadcrumb blocks into this plugin's block.
 *
 * Each transform spreads the source attributes so the shared block supports
 * (`align`, spacing, color, border, typography, etc.) carry over, then remaps
 * the source's semantic attributes onto this block's names. `createBlock()`
 * sanitizes against this block's registered attributes, so source-only keys
 * with no equivalent here are dropped automatically.
 *
 * From `core/breadcrumbs`, that leaves `separator` (a literal glyph vs. this
 * block's icon keys) and `prefersTaxonomy` (a global boolean vs. this block's
 * per-post-type `postTaxonomy`/`mapRewriteTags`) behind — neither has a
 * reliable equivalent — while `woocommerce/breadcrumbs` maps in full.
 * `yoast-seo/breadcrumbs` carries no attributes beyond `className`, so its
 * conversion falls back entirely to this block's defaults.
 */
export default {
	from: [
		{
			type: 'block',
			blocks: ['core/breadcrumbs'],
			transform: (attributes) =>
				createBlock('x3p0/breadcrumbs', {
					...attributes,
					showOnHomepage: attributes.showOnHomePage,
					showTrailEnd: attributes.showCurrentItem,
					showTrailStart: attributes.showHomeItem
				})
		},
		{
			type: 'block',
			blocks: ['woocommerce/breadcrumbs'],
			transform: (attributes) =>
				createBlock('x3p0/breadcrumbs', {
					...attributes,
					justifyContent: attributes.contentJustification
				})
		},
		{
			type: 'block',
			blocks: ['yoast-seo/breadcrumbs'],
			transform: (attributes) =>
				createBlock('x3p0/breadcrumbs', {...attributes})
		}
	]
};
