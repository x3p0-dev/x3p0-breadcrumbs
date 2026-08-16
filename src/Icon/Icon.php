<?php

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use const X3P0\Breadcrumbs\PLUGIN_DIR;

/**
 * Enum of icons registered with and used throughout the plugin. Each case
 * represents a single SVG icon shipped with the plugin. The case's backed
 * string value doubles as both the icon's file name (without the `.svg`
 * extension) and the suffix used to build its registration handle. See
 * {@see self::name()} and {@see self::filePath()}.
 */
enum Icon: string
{
	case Arrow            = 'arrow';
	case Chevron          = 'chevron';
	case ChevronDouble    = 'chevron-double';
	case EmojiHouse       = 'emoji-house';
	case EmojiHouses      = 'emoji-houses';
	case EmojiHouseGarden = 'emoji-house-garden';
	case HomeFill         = 'home-fill';
	case HomeOutline      = 'home-outline';
	case HouseFill        = 'house-fill';
	case HouseOutline     = 'house-outline';
	case Triangle         = 'triangle';

	/**
	 * Collection namespace for all registered icons. Used as the prefix for
	 * each icon's registration handle and as the name under which the icon
	 * collection itself is registered.
	 *
	 * @var string
	 */
	public const COLLECTION = 'x3p0-breadcrumbs';

	/**
	 * Absolute path to the icon folder where the SVGs are stored.
	 *
	 * @var string
	 */
	private const ICONS_PATH = PLUGIN_DIR . '/public/media/svg';

	/**
	 * Returns the icon's translated label. Used as the human-readable name
	 * shown for the icon wherever WordPress displays registered icons
	 * (e.g., the icon picker).
	 */
	public function label(): string
	{
		return match ($this) {
			self::Arrow            => __('Arrow', 'x3p0-breadcrumbs'),
			self::Chevron          => __('Chevron', 'x3p0-breadcrumbs'),
			self::ChevronDouble    => __('Chevron Double', 'x3p0-breadcrumbs'),
			self::EmojiHouse       => __('Emoji: House', 'x3p0-breadcrumbs'),
			self::EmojiHouseGarden => __('Emoji: House With Garden', 'x3p0-breadcrumbs'),
			self::EmojiHouses      => __('Emoji: Houses', 'x3p0-breadcrumbs'),
			self::Triangle         => __('Triangle', 'x3p0-breadcrumbs'),
			self::HomeFill         => __('Home: Filled', 'x3p0-breadcrumbs'),
			self::HomeOutline      => __('Home: Outlined', 'x3p0-breadcrumbs'),
			self::HouseFill        => __('House: Filled', 'x3p0-breadcrumbs'),
			self::HouseOutline     => __('House: Outlined', 'x3p0-breadcrumbs')
		};
	}

	/**
	 * Returns the icon's full name with the namespace for registration.
	 * Combines {@see self::COLLECTION} with the case's value (e.g.,
	 * `devblog-restaurant/bakery`) to form the handle WordPress uses to
	 * identify the icon when registered via `wp_register_icon()`.
	 */
	public function name(): string
	{
		return self::COLLECTION . '/' . $this->value;
	}

	/**
	 * Returns the absolute file path to the icon's SVG file. Combines
	 * {@see self::ICONS_PATH} with the case's value to locate the
	 * icon's SVG source file on disk.
	 */
	public function filePath(): string
	{
		return self::ICONS_PATH . '/icon-' . $this->value . '.svg';
	}
}
