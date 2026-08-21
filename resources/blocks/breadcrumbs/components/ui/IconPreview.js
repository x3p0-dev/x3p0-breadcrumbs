/**
 * Icon preview component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { safeHTML } from '@wordpress/dom';

/**
 * Renders a registered icon's SVG markup, which the REST API returns as a
 * sanitized string rather than JSX and so has to be injected.
 *
 * Kept a component rather than a bare element because `Button` hands its `icon`
 * prop to `Icon`, which clones a plain element with `size`/`width`/`height`
 * props — meaningless on a `span`, and passed straight through to the DOM. A
 * component absorbs them instead, and the size comes from CSS, alongside the
 * picker's own styles in `scss/icon-library`.
 * @param props
 * @returns {JSX.Element}
 */
export const IconPreview = ({content}) => (
	<span
		className="x3p0-breadcrumbs-icon-preview"
		dangerouslySetInnerHTML={{__html: safeHTML(content)}}
	/>
);
