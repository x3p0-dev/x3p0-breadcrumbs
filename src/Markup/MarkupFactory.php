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

use X3P0\Breadcrumbs\Markup\Markup;
use X3P0\Breadcrumbs\Markup\MarkupRegistry;

/**
 * Factory class for making markup objects.
 */
class MarkupFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(private MarkupRegistry $markupRegistry)
	{}

	/**
	 * Creates an instance of a markup object.
	 */
	public function make(string $name, array $params = []): ?Markup
	{
		if ($this->markupRegistry->isRegistered($name)) {
			$markup = $this->markupRegistry->get($name);
			return new $markup(...$params);
		}

		return null;
	}
}
