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
 * key namespacing (`IconOptionKey::postType()` and its siblings), and having
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

/**
 * The option keys the editor itself names, mirroring the cases of the
 * `IconOptionKey` enum on the PHP side. Everything else the panel renders is
 * driven by `ICON_OPTIONS` and needs no key spelled out here; these three are
 * the ones the editor branches on — the separator gets a control of its own
 * and outlives the icon visibility setting, the home crumb is pinned beside
 * it, and the page post type backs the canvas placeholder crumbs.
 *
 * PHP enums do not cross the wire, so this is a hand-written mirror. It is
 * kept to the keys with a consumer rather than reproducing the whole enum: a
 * mirrored key nothing reads is a copy that can rot unnoticed. Note that a
 * derived key is spelled out whole, not rebuilt from its parts, for the reason
 * given above — the namespacing scheme stays PHP's alone.
 */
export const ICON_OPTION_KEYS = Object.freeze({
	SEPARATOR:      'separator',
	HOME:           'home',
	POST_TYPE_PAGE: 'post-type:page'
});

/**
 * The icon option key registered for each viewable post type's single-post
 * crumbs, keyed by post type name — see `BlockAssets::postTypeIconKeys()`. The
 * canvas previews the icon of whatever post is open by looking its post type up
 * here and resolving the key it finds.
 *
 * PHP resolves these for the same reason it resolves `group` and `slug` above:
 * `IconOptionKey` owns the `post-type:{slug}` scheme, and rebuilding it here
 * would be a second copy of it. The two fields the options already carry are no
 * substitute — an option can be regrouped after registration (WooCommerce moves
 * its product options into a group of its own), and a post type's archive
 * option carries the very same slug as its singular one, so neither field
 * identifies which option a post belongs to.
 */
// noinspection JSUnresolvedVariable
export const POST_TYPE_ICON_OPTION_KEYS = window.x3p0Breadcrumbs?.postTypeIconKeys ?? {};
