/**
 * Meta keys.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

/**
 * The plugin's registered meta keys, as `{icon: '…'}` — the cases of the
 * `MetaKey` enum on the PHP side, assembled by `MetaKey::forEditor()` and
 * handed over by both `BlockAssets` and `EditorAssets`, since the block canvas
 * and the Summary panel's icon row each read post meta.
 *
 * These arrive from PHP rather than being mirrored here the way
 * `ICON_OPTION_KEYS` mirrors `IconOptionKey`. A drifted icon option key
 * degrades to the fallback option and is visible on screen; a drifted meta key
 * is a database column name, so the editor would quietly read and write meta
 * nothing else in the plugin ever looks at.
 */
// noinspection JSUnresolvedVariable
export const META_KEYS = window.x3p0Breadcrumbs?.metaKeys ?? {};
