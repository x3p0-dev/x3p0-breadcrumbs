<?php

/**
 * Icon preset key enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The icon preset keys this plugin owns. A key names something that can carry
 * an icon: the separator, the home crumb, single posts of a given post type.
 * It is what every part of the icon system works against — crumbs name the
 * keys they resolve through, {@see IconPresets} holds a preset per key,
 * {@see IconConfig} records what the site owner chose for one, and the block
 * editor offers a control per preset that carries a label.
 */
enum IconPresetKey: string implements IconPresetDefinition
{
	case Separator     = 'separator';
	case Home          = 'home';
	case Date          = 'date';
	case Time          = 'time';
	case User          = 'user';
	case Search        = 'search';
	case Error404      = 'error-404';
	case Paged         = 'paged';
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
	 * Family prefix for the keys derived per post type. The `:` separator
	 * keeps derived keys visually distinct from icon *references*, which
	 * use `/`.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const POST_TYPE = 'post-type';

	/**
	 * Family prefix for the keys derived per post type archive.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const POST_TYPE_ARCHIVE = 'post-type-archive';

	/**
	 * Family prefix for the keys derived per taxonomy.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const TAXONOMY = 'taxonomy';

	/**
	 * Returns the icon this key is preset to — what it renders when nothing
	 * more deliberate names one: an {@see Icon} case for an icon this plugin
	 * bundles, or a `{collection}/{name}` reference for anyone else's.
	 */
	public function icon(): Icon|string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Separator     => Icon::Chevron,
			self::Home          => 'core/home',
			self::Date          => 'core/calendar',
			self::Time          => 'core/scheduled',
			self::User          => 'core/people',
			self::Search        => 'core/search',
			self::Error404      => 'core/error',
			self::Paged         => Icon::Description,
			self::PrivacyPolicy => 'core/shield',
			self::NetworkSite   => 'core/desktop',
			self::MediaImage    => 'core/image',
			self::MediaAudio    => 'core/audio',
			self::MediaVideo    => 'core/capture-video',
			self::Archive,
			self::PostsPage     => Icon::Archive,
			self::Custom,
			self::Fallback      => Icon::Article
		};
	}

	/**
	 * Returns the translated label for this key's block editor control, or
	 * an empty string for a key that carries a preset but isn't worth a
	 * control of its own. The network site key is labeled only on a network,
	 * since its crumb can only appear on one.
	 */
	public function label(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Separator     => __('Separator', 'x3p0-breadcrumbs'),
			self::Home          => __('Home', 'x3p0-breadcrumbs'),
			self::Date          => __('Date archives', 'x3p0-breadcrumbs'),
			self::Time          => __('Time archives', 'x3p0-breadcrumbs'),
			self::User          => __('User', 'x3p0-breadcrumbs'),
			self::Search        => __('Search', 'x3p0-breadcrumbs'),
			self::Error404      => __('Page not found', 'x3p0-breadcrumbs'),
			self::Paged         => __('Pagination', 'x3p0-breadcrumbs'),
			self::MediaImage    => __('Image', 'x3p0-breadcrumbs'),
			self::MediaAudio    => __('Audio', 'x3p0-breadcrumbs'),
			self::MediaVideo    => __('Video', 'x3p0-breadcrumbs'),
			self::NetworkSite   => is_multisite() ? __('Network Site', 'x3p0-breadcrumbs') : '',
			self::Archive,
			self::Custom,
			self::Fallback,
			self::PostsPage,
			self::PrivacyPolicy => ''
		};
	}

	/**
	 * Returns the group the block editor lists this key's control under.
	 */
	public function group(): IconGroup
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::MediaImage,
			self::MediaAudio,
			self::MediaVideo => IconGroup::Media,
			default          => IconGroup::General
		};
	}

	/**
	 * @inheritDoc
	 */
	public function presetKey(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return $this->value;
	}

	/**
	 * Builds the key for a post type's single-post crumbs.
	 */
	public static function postType(string $postType): string
	{
		return self::POST_TYPE . ':' . $postType;
	}

	/**
	 * Builds the key for a post type's archive crumb.
	 */
	public static function postTypeArchive(string $postType): string
	{
		return self::POST_TYPE_ARCHIVE . ':' . $postType;
	}

	/**
	 * Builds the key for a taxonomy's term crumbs.
	 */
	public static function taxonomy(string $taxonomy): string
	{
		return self::TAXONOMY . ':' . $taxonomy;
	}

	/**
	 * Reduces a key to the string everything else stores it under, passing
	 * a raw string through untouched. Takes any {@see IconPresetDefinition},
	 * not just this enum, so an extension's own keys arrive as cases rather
	 * than as strings it had to convert first.
	 */
	public static function normalize(IconPresetDefinition|string $key): string
	{
		return $key instanceof IconPresetDefinition ? $key->presetKey() : $key;
	}
}
