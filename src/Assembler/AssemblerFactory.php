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
 * Factory class used for creating new assembler instances from a registry of
 * assembler types.
 */
final class AssemblerFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(private AssemblerRegistry $assemblerRegistry)
	{}

	/**
	 * Creates an instance of an assembler object.
	 */
	public function make(string $key, array $params = []): ?Assembler
	{
		if ($assembler = $this->assemblerRegistry->get($key)) {
			return new $assembler(...$params);
		}

		return null;
	}
}
