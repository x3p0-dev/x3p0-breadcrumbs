<?php

/**
 * Markup factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Factory class for making markup objects.
 */
final class MarkupFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(private readonly MarkupRegistry $markupRegistry)
	{}

	/**
	 * Creates an instance of a markup object.
	 */
	public function make(string $key, array $params = []): ?Markup
	{
		if ($markup = $this->markupRegistry->get($key)) {
			return new $markup(...$params);
		}

		return null;
	}
}
