/**
 * Registers the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Import stylesheets.
import './scss/index.scss';
import './scss/style.scss';

// Import dependencies.
import { registerBlockType } from '@wordpress/blocks';
import metadata              from './block.json';
import icon                  from './js/block-icon';
import edit                  from './js/block-edit';

// Register block type.
registerBlockType(metadata, { icon, edit });
