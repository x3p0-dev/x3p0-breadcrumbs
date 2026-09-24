<?php

/**
 * Icon enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use const X3P0\Breadcrumbs\PLUGIN_DIR;

/**
 * Enum of the SVG icons shipped with the plugin. Each case's backed value
 * doubles as the icon's file name and the suffix of its registration handle.
 */
enum Icon: string
{
	case Archive           = 'archive';
	case Arrow             = 'arrow';
	case Article           = 'article';
	case Box               = 'box';
	case BrandingWatermark = 'branding-watermark';
	case Category          = 'category';
	case Chevron           = 'chevron';
	case ChevronDouble     = 'chevron-double';
	case Color             = 'color';
	case Description       = 'description';
	case EmojiHouse        = 'emoji-house';
	case EmojiHouses       = 'emoji-houses';
	case EmojiHouseGarden  = 'emoji-house-garden';
	case HomeFill          = 'home-fill';
	case HomeOutline       = 'home-outline';
	case HouseFill         = 'house-fill';
	case HouseOutline      = 'house-outline';
	case List              = 'list';
	case Package           = 'package';
	case ReceiptLong       = 'receipt-long';
	case Shipping          = 'shipping';
	case Straighten        = 'straighten';
	case Triangle          = 'triangle';
	case Unseen            = 'unseen';

	/**
	 * Collection namespace for all registered icons.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const COLLECTION = 'x3p0-breadcrumbs';

	/**
	 * Absolute path to the folder where the SVGs are stored.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const ICONS_PATH = PLUGIN_DIR . '/public/media/svg';

	/**
	 * Returns the icon's translated label.
	 */
	public function label(): string
	{
		return match ($this) {
			self::Archive           => __('Archive', 'x3p0-breadcrumbs'),
			self::Arrow             => __('Arrow', 'x3p0-breadcrumbs'),
			self::Article           => __('Article', 'x3p0-breadcrumbs'),
			self::Box               => __('Box', 'x3p0-breadcrumbs'),
			self::BrandingWatermark => __('Branding Watermark', 'x3p0-breadcrumbs'),
			self::Category          => __('Category', 'x3p0-breadcrumbs'),
			self::Chevron           => __('Chevron', 'x3p0-breadcrumbs'),
			self::ChevronDouble     => __('Chevron Double', 'x3p0-breadcrumbs'),
			self::Color             => __('Color', 'x3p0-breadcrumbs'),
			self::Description       => __('Description', 'x3p0-breadcrumbs'),
			self::EmojiHouse        => __('Emoji: House', 'x3p0-breadcrumbs'),
			self::EmojiHouseGarden  => __('Emoji: House With Garden', 'x3p0-breadcrumbs'),
			self::EmojiHouses       => __('Emoji: Houses', 'x3p0-breadcrumbs'),
			self::HomeFill          => __('Home: Filled', 'x3p0-breadcrumbs'),
			self::HomeOutline       => __('Home: Outlined', 'x3p0-breadcrumbs'),
			self::HouseFill         => __('House: Filled', 'x3p0-breadcrumbs'),
			self::HouseOutline      => __('House: Outlined', 'x3p0-breadcrumbs'),
			self::List              => __('List', 'x3p0-breadcrumbs'),
			self::Package           => __('Package', 'x3p0-breadcrumbs'),
			self::ReceiptLong       => __('Receipt: Long', 'x3p0-breadcrumbs'),
			self::Shipping          => __('Shipping', 'x3p0-breadcrumbs'),
			self::Straighten        => __('Straighten', 'x3p0-breadcrumbs'),
			self::Triangle          => __('Triangle', 'x3p0-breadcrumbs'),
			self::Unseen            => __('Unseen', 'x3p0-breadcrumbs')
		};
	}

	/**
	 * Returns the icon's namespaced registration handle, as
	 * `{collection}/{name}`.
	 */
	public function name(): string
	{
		return self::COLLECTION . '/' . $this->value;
	}

	/**
	 * Returns the absolute path to the icon's SVG file.
	 */
	public function filePath(): string
	{
		return self::ICONS_PATH . '/icon-' . $this->value . '.svg';
	}
}
