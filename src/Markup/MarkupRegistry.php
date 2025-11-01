<?php

/**
 * Markup registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use TypeError;

/**
 * Stores the markup classes that can later be instantiated as objects.
 */
final class MarkupRegistry
{
	/**
	 * Stores the array of markup classes.
	 */
	protected array $markups = [];

	/**
	 * Allows registering a default set of markups.
	 */
	public function __construct(array $markups = [])
	{
		foreach ($markups as $key => $markupClass) {
			$this->register($key, $markupClass);
		}
	}

	/**
	 * Registers a markup class.
	 *
	 * @param class-string<Markup> $markupClass
	 */
	public function register(string $key, string $markupClass): void
	{
		if (! is_subclass_of($markupClass, Markup::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-breadcrumbs'),
				Markup::class
			)));
		}

		$this->markups[$key] = $markupClass;
	}

	/**
	 * Unregisters a markup class.
	 */
	public function unregister(string $key): void
	{
		unset($this->markups[$key]);
	}

	/**
	 * Checks if a markup is registered.
	 */
	public function isRegistered(string $key): bool
	{
		return isset($this->markups[$key]);
	}

	/**
	 * Returns a markup class string or `null`.
	 *
	 * @return null|class-string<Markup>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->markups[$key] : null;
	}
}
