<?php

/**
 * Markup factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Resolves a markup key to its registered class via the registry and
 * instantiates it. Returns `null` when the key is not registered.
 */
final class MarkupFactory
{
	/**
	 * Stores the registry used to look up markup classes by key.
	 */
	public function __construct(private readonly MarkupRegistry $markupRegistry)
	{}

	/**
	 * Instantiates the markup class registered for a given key, passing the
	 * supplied params to its constructor. Returns `null` for an unknown key.
	 */
	public function make(string $key, array $params = []): ?Markup
	{
		if ($markup = $this->markupRegistry->get($key)) {
			return new $markup(...$params);
		}

		return null;
	}
}
