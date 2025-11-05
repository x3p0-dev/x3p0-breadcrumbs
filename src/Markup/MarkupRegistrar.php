<?php

/**
 * Markup registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Registers markup classes with the registry.
 */
final class MarkupRegistrar
{
	public const HTML      = 'html';
	public const MICRODATA = 'microdata';
	public const RDFA      = 'rdfa';
	public const JSON_LD   = 'json-ld';

	/**
	 * An array of markup keys and their associated classes, to be stored
	 * in the markup registry.
	 */
	private static function getMarkups(): array
	{
		return [
			self::HTML      => Type\Html::class,
			self::MICRODATA => Type\Microdata::class,
			self::RDFA      => Type\Rdfa::class,
			self::JSON_LD   => Type\JsonLinkedData::class,
		];
	}

	/**
	 * Registers default markups with the registry.
	 */
	public static function register(MarkupRegistry $markupRegistry): void
	{
		foreach (self::getMarkups() as $key => $markupClass) {
			if (! $markupRegistry->isRegistered($key)) {
				$markupRegistry->register($key, $markupClass);
			}
		}
	}
}
