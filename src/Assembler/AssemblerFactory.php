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

use X3P0\Breadcrumbs\Contracts;

/**
 * Factory class used for creating new assembler instances from a registry of
 * assembler types.
 */
class AssemblerFactory implements Contracts\AssemblerFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(
		private Contracts\AssemblerTypeRegistry $assemblerTypes
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function make(string $name, array $params = []): ?Assembler
	{
		if ($this->assemblerTypes->isRegistered($name)) {
			$assembler = $this->assemblerTypes->get($name);
			return new $assembler(...$params);
		}

		return null;
	}
}
