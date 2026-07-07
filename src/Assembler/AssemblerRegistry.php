<?php

/**
 * Assembler registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\ClassRegistry;

/**
 * Stores the `string key => assembler class name` mappings that the factory
 * resolves against. Registration is validated so that only subclasses of the
 * abstract `Assembler` can be stored.
 *
 * @extends ClassRegistry<Assembler>
 */
final class AssemblerRegistry extends ClassRegistry
{
	/**
	 * @inheritDoc
	 */
	protected function type(): string
	{
		return Assembler::class;
	}
}
