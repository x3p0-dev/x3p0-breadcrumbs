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
 * Registers classes with the markup registry.
 */
final class MarkupRegistrar
{
	/**
	 * An array of markup keys and their associated classes, to be stored
	 * in the markup registry.
	 */
	private const MARKUPS = [
		'html'      => Type\Html::class,
		'microdata' => Type\Microdata::class,
		'rdfa'      => Type\Rdfa::class,
		'json-ld'   => Type\JsonLinkedData::class
	];

	/**
	 * Registers default markups with the registry.
	 */
	public static function register(MarkupRegistry $markupRegistry): void
	{
		foreach (self::MARKUPS as $key => $markupClass) {
			if (! $markupRegistry->isRegistered($key)) {
				$markupRegistry->register($key, $markupClass);
			}
		}
	}
}
