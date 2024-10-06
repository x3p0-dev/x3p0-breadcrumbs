/**
 * Registers the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2023, Justin Tadlock
 * @license   GPL-3.0-or-later
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
