/**
 * Block edit.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import BreadcrumbsBlockControls     from './controls/block';
import BreadcrumbsInspectorControls from './controls/inspector';
import BreadcrumbsContent           from './content';

export default (props) => (
	<>
		<BreadcrumbsBlockControls {...props}/>
		<BreadcrumbsInspectorControls {...props}/>
		<BreadcrumbsContent {...props}/>
	</>
);
