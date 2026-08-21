<?php

/**
 * Meta key enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Meta;

/**
 * Enum of post/term meta keys registered by the plugin. Each case's backed
 * string value is the literal meta key stored in the database.
 */
enum MetaKey: string
{
	case Icon = 'x3p0-breadcrumbs-icon';

	/**
	 * Returns the meta keys handed to the editor scripts, named rather than
	 * listed so the JavaScript reaches for `metaKeys.icon` instead of matching
	 * on the literal it is trying not to know. Both editor bundles read post
	 * meta — the block previews the open post's icon on its last crumb, and the
	 * Summary panel's row writes it — so the pair is assembled once here rather
	 * than in each of the classes that pass it over.
	 *
	 * @return array<string, string>
	 */
	public static function forEditor(): array
	{
		return [
			'icon' => self::Icon->value
		];
	}
}
