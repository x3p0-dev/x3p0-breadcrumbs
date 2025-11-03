/**
 * Block edit.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import BreadcrumbsToolbar   from './controls/block/BreadcrumbsToolbar';
import BreadcrumbsInspector from './controls/inspector/BreadcrumbsInspector';
import BreadcrumbsContent   from './content/BreadcrumbsContent';

/**
 * Renders the block edit component.
 * @param props
 * @returns {JSX.Element}
 */
export default (props) => (
	<>
		<BreadcrumbsToolbar {...props}/>
		<BreadcrumbsInspector {...props}/>
		<BreadcrumbsContent {...props}/>
	</>
);
