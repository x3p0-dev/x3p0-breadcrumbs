/**
 * Block icon.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import {G, Polygon, Rect, SVG} from "@wordpress/primitives";

/**
 * Renders the block icon.
 * @returns {JSX.Element}
 */
export default () => (
	<SVG xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
		<G><Rect fill="none" height="24" width="24"/></G>
		<G>
			<Polygon points="15.5,5 11,5 16,12 11,19 15.5,19 20.5,12"/>
			<Polygon points="8.5,5 0,5 0,12 0,19 8.5,19 13.5,12"/>
		</G>
	</SVG>
);
