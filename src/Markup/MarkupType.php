<?php

/**
 * Markup type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Enumerates the canonical markup keys.
 */
enum MarkupType: string
{
	case Html           = 'html';
	case Microdata      = 'microdata';
	case Rdfa           = 'rdfa';
	case JsonLinkedData = 'json-ld';

	/**
	 * Returns the markup class associated with the type.
	 *
	 * @return class-string<Markup>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Html           => Type\Html::class,
			self::Microdata      => Type\Microdata::class,
			self::Rdfa           => Type\Rdfa::class,
			self::JsonLinkedData => Type\JsonLinkedData::class
		};
	}
}
