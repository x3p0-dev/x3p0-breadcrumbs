<?php

/**
 * Crumb factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Resolves a crumb type key against the registry and instantiates the mapped
 * class. This is the single entry point for creating crumbs, keeping callers
 * decoupled from concrete `Type` class names.
 */
final class CrumbFactory
{
	/**
	 * Stores the registry used to look up crumb classes by key.
	 */
	public function __construct(private readonly CrumbRegistry $crumbRegistry)
	{}

	/**
	 * Builds the crumb registered under the given type, forwarding `$params`
	 * to its constructor. Accepts a `CrumbType` for built-in crumbs or a string
	 * key for custom ones. Returns null when the key is not registered.
	 */
	public function make(CrumbType|string $type, array $params = []): ?Crumb
	{
		$key = $type instanceof CrumbType ? $type->value : $type;

		if ($crumb = $this->crumbRegistry->get($key)) {
			return new $crumb(...$params);
		}

		return null;
	}
}
