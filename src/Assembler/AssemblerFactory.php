<?php

/**
 * Assembler factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Packages\Framework\Container\ServiceResolver;

/**
 * Builds a `Assembler` instance from a type identifier. Returns `null` when
 * resolution is not successful, so callers can dispatch optimistically.
 */
final class AssemblerFactory
{
	/**
	 * Stores the resolver that builds the mapped class through the container.
	 */
	public function __construct(private readonly ServiceResolver $resolver)
	{}

	/**
	 * Builds the assembler for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 *
	 * Construct the type via a class-string or an enum that implements the
	 * {@see AssemblerDefinition} interface (the class can be derived from
	 * the enum).
	 */
	public function make(AssemblerDefinition|string $type, array $params = []): ?Assembler
	{
		$type = is_string($type) ? $type : $type->className();

		if (is_subclass_of($type, Assembler::class)) {
			/** @var Assembler */
			return $this->resolver->make($type, $params);
		}

		return null;
	}
}
