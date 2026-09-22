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
 * The icon option keys this plugin owns, each able to hand over the option it
 * ships with. The set is open — extensions register keys of their
 * own, and one per registered post type and taxonomy is derived at runtime —
 * so anywhere a key is accepted, the type is `IconOptionKey|string`.
 *
 * What `option()` answers is what `IconOptionRegistrar` seeds the
 * {@see IconOptionRegistry} with. Once seeded, the registry is what the block
 * editor lists and what renders — an extension may have retargeted a built-in
 * — so consumers read the registry (or `IconOptionResolver`), not the case.
 *
 * The values are the wire format: they key the block's `icons` attribute, so
 * they are saved into post content and read back by the editor (see
 * `utils/icon-options.js`, which mirrors the cases it branches on). Renaming a
 * case value is a migration, not a refactor.
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
	 * Returns the option this key ships with: its default icon (an
	 * {@see Icon} case for an icon this plugin bundles, or a
	 * `{collection}/{name}` library reference for anyone else's), its
	 * translated label, and the group the block editor lists it under.
	 *
	 * Every icon the plugin can render is an option, but not every one is
	 * worth a block control: the unlabeled options carry a default and
	 * nothing more. The network site option is labeled only on a network,
	 * since its crumb can only appear on one.
	 */
	public function option(): IconOption
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Separator     => new IconOption(Icon::Chevron, __('Separator', 'x3p0-breadcrumbs')),
			self::Home          => new IconOption('core/home', __('Home', 'x3p0-breadcrumbs')),
			self::Date          => new IconOption('core/calendar', __('Date archives', 'x3p0-breadcrumbs')),
			self::Time          => new IconOption('core/scheduled', __('Time archives', 'x3p0-breadcrumbs')),
			self::User          => new IconOption('core/people', __('User', 'x3p0-breadcrumbs')),
			self::Search        => new IconOption('core/search', __('Search', 'x3p0-breadcrumbs')),
			self::Error404      => new IconOption('core/error', __('Page not found', 'x3p0-breadcrumbs')),
			self::Paged         => new IconOption(Icon::Description, __('Pagination', 'x3p0-breadcrumbs')),
			self::PrivatePost   => new IconOption(Icon::Unseen, __('Private', 'x3p0-breadcrumbs')),
			self::ProtectedPost => new IconOption('core/key', __('Password protected', 'x3p0-breadcrumbs')),
			self::PrivacyPolicy => new IconOption('core/shield'),
			self::Archive,
			self::PostsPage     => new IconOption(Icon::Archive),
			self::Custom,
			self::Fallback      => new IconOption(Icon::Article),
			self::NetworkSite   => new IconOption(
				'core/desktop',
				is_multisite() ? __('Network Site', 'x3p0-breadcrumbs') : ''
			),
			self::MediaImage    => new IconOption('core/image', __('Image', 'x3p0-breadcrumbs'), IconOptionGroupKey::Media),
			self::MediaAudio    => new IconOption('core/audio', __('Audio', 'x3p0-breadcrumbs'), IconOptionGroupKey::Media),
			self::MediaVideo    => new IconOption('core/capture-video', __('Video', 'x3p0-breadcrumbs'), IconOptionGroupKey::Media)
		};
	}

	/**
	 * Builds the option key for a post type's single-post crumbs. The `:`
	 * namespace separator keeps derived keys visually distinct from icon
	 * *references*, which use `/`.
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
	 * passing a raw string through untouched.
	 */
	public static function normalize(IconOptionKey|string $key): string
	{
		return $key instanceof self ? $key->value : $key;
	}
}
