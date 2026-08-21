/**
 * Registers the plugin's editor UI.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Import stylesheets.
import './scss/index.scss';

// Import dependencies.
import { registerPlugin } from '@wordpress/plugins';

// Import the components.
import { IconRow } from './components/IconRow';

// Register the plugin's editor UI. `EditorAssets` loads this script only on
// the post editor and only for post types whose meta the editor can reach, so
// nothing here has to ask again.
registerPlugin('x3p0-breadcrumbs', {
	render: IconRow
});
