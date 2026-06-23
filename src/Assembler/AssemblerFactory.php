<?php

/**
 * Assembler factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

/**
 * Resolves an assembler key against the registry and instantiates the matching
 * class. This is the single entry point for creating assemblers so that callers
 * never reference concrete `Type\*` classes directly.
 */
final class AssemblerFactory
{
	/**
	 * Stores the registry that maps assembler keys to their class names.
	 */
	public function __construct(private readonly AssemblerRegistry $assemblerRegistry)
	{}

	/**
	 * Looks up the class registered for the given key and returns a new
	 * instance built from `$params`. Returns `null` when no assembler is
	 * registered under the key.
	 */
	public function make(string $key, array $params = []): ?Assembler
	{
		if ($assembler = $this->assemblerRegistry->get($key)) {
			return new $assembler(...$params);
		}

		return null;
	}
}
