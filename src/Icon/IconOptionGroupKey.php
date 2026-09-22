<?php

/**
 * Icon option group key enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The groups this plugin sorts its block editor icon controls into, each able
 * to hand over the group it ships with. The set is open — extensions register
 * groups of their own alongside them — so anywhere a group key is accepted,
 * the type is `IconOptionGroupKey|string`, on the same terms as
 * {@see IconOptionKey}. `IconOptionRegistrar` seeds the
 * {@see IconOptionGroupRegistry} from these cases.
 */
enum IconOptionGroupKey: string
{
	case General         = 'general';
	case PostType        = 'post-types';
	case PostTypeArchive = 'post-type-archives';
	case Taxonomy        = 'taxonomies';
	case Media           = 'media';

	/**
	 * Returns the group this key ships with, carrying its translated heading
	 * as listed in the block editor.
	 */
	public function group(): IconOptionGroup
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::General         => new IconOptionGroup(__('General', 'x3p0-breadcrumbs')),
			self::PostType        => new IconOptionGroup(__('Post Types', 'x3p0-breadcrumbs')),
			self::PostTypeArchive => new IconOptionGroup(__('Post Type Archives', 'x3p0-breadcrumbs')),
			self::Taxonomy        => new IconOptionGroup(__('Taxonomies', 'x3p0-breadcrumbs')),
			self::Media           => new IconOptionGroup(__('Media', 'x3p0-breadcrumbs'))
		};
	}

	/**
	 * Reduces a key to the string the registry stores it under, passing a
	 * raw string through untouched.
	 */
	public static function normalize(IconOptionGroupKey|string $key): string
	{
		return $key instanceof self ? $key->value : $key;
	}
}
