<?php

/**
 * Icon option key enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The icon option keys this plugin owns. The set of keys is open — extensions
 * register options under keys of their own, and one key per registered post
 * type and taxonomy is derived at runtime — so anywhere a key is accepted, the
 * type is `IconOptionKey|string`: a case for the ones named here, a raw string
 * for everyone else's. This mirrors how {@see Icon} names the icons this plugin
 * ships within the open namespace of every registered icon.
 *
 * Naming the closed set is what keeps the vocabulary from being a scattering of
 * literals. A key is written down in one place (`IconOptionRegistrar`, which
 * registers the option) and read in another (a `Crumb` or the `Markup` layer,
 * which resolves it); as bare strings those two had nothing linking them, and a
 * rename on either side degraded silently to the `Fallback` option rather than
 * failing. The `Separator` case, for one, is written in `IconOptionRegistrar`
 * and read in `Markup\Type\Html`.
 *
 * The enum is backed because these values are the wire format: they key the
 * block's `icons` attribute, so they are saved into post content and read back
 * by the editor through `window.x3p0Breadcrumbs` (see `utils/icon-options.js`,
 * which mirrors the cases the editor names). Renaming a case value is a
 * migration, not a refactor.
 *
 * Keys derived from a WordPress object have no case they could be — a site's
 * post types and taxonomies are not known until `init` — so they are built by
 * the static methods below instead. Those return strings rather than cases,
 * which is the same open/closed split the union above expresses.
 */
enum IconOptionKey: string
{
	case Separator     = 'separator';
	case Home          = 'home';
	case Date          = 'date';
	case Time          = 'time';
	case User          = 'user';
	case Search        = 'search';
	case Error404      = 'error-404';
	case Paged         = 'paged';
	case PrivatePost   = 'private-post';
	case ProtectedPost = 'protected-post';
	case Archive       = 'archive';
	case Custom        = 'custom';
	case Fallback      = 'fallback';
	case PrivacyPolicy = 'privacy-policy';
	case PostsPage     = 'posts-page';
	case NetworkSite   = 'network-site';
	case MediaImage    = 'media-image';
	case MediaAudio    = 'media-audio';
	case MediaVideo    = 'media-video';

	/**
	 * Builds the option key for a post type's single-post crumbs. Consumers
	 * resolving an icon need the key alone (see `Crumb::iconOptionKey()`), so
	 * this stays separate from the `IconOption::forPostType()` constructor
	 * that uses it.
	 *
	 * The `:` namespace separator keeps derived keys visually distinct from
	 * icon *references*, which use `/`.
	 */
	public static function postType(string $postType): string
	{
		return 'post-type:' . $postType;
	}

	/**
	 * Builds the option key for a post type's archive crumb.
	 */
	public static function postTypeArchive(string $postType): string
	{
		return 'post-type-archive:' . $postType;
	}

	/**
	 * Builds the option key for a taxonomy's term crumbs.
	 */
	public static function taxonomy(string $taxonomy): string
	{
		return 'taxonomy:' . $taxonomy;
	}

	/**
	 * Reduces a key to the string the registry and config store it under,
	 * passing a raw string through untouched. Called at the boundary of every
	 * method accepting the `IconOptionKey|string` union, so the union is
	 * resolved once, on the way in, and nothing downstream carries it.
	 */
	public static function normalize(IconOptionKey|string $key): string
	{
		return $key instanceof self ? $key->value : $key;
	}
}
