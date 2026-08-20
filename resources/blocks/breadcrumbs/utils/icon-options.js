/**
 * Icon options.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

/**
 * The registered icon options, as `{key, icon, name, group, slug}` records —
 * see `IconOptionRegistry::forBlock()` on the PHP side. PHP enumerates
 * everything, including one option per viewable post type and public taxonomy
 * (via `IconOptionRegistrar`), so the editor never enumerates them
 * client-side and a newly registered option becomes available with no JS
 * change needed.
 *
 * `group` and `slug` arrive resolved rather than derived here. PHP owns the
 * key namespacing (`IconOption::postTypeKey()` and its siblings), and having
 * the editor parse those prefixes back apart would be a second copy of that
 * scheme, silently wrong the moment PHP's changed.
 */
// noinspection JSUnresolvedVariable
export const ICON_OPTIONS = window.x3p0Breadcrumbs?.iconOptions ?? [];

/**
 * The groups the options are sorted into, as `{key, name}` pairs in the order
 * they should be listed. Built-ins come first, then any an extension
 * registered for a family of its own options.
 */
// noinspection JSUnresolvedVariable
export const OPTION_GROUPS = window.x3p0Breadcrumbs?.iconOptionGroups ?? [];
