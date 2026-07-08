<?php

/**
 * Assembler registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Support\EnumRegistrar;

/**
 * Seeds the registry with the plugin's built-in assembler types on boot. Each
 * case of the `AssemblerType` enum is mapped to its corresponding concrete
 * class so the factory can resolve them by key.
 */
final class AssemblerRegistrar extends EnumRegistrar
{
	/**
	 * The enum whose cases seed the assembler registry.
	 *
	 * @var  class-string<AssemblerType>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ENUM = AssemblerType::class;

	/**
	 * Type-hints the assembler registry so the container injects it, then
	 * hands it to the base registrar.
	 */
	public function __construct(AssemblerRegistry $registry)
	{
		parent::__construct($registry);
	}
}
