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

use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Resolves an assembler key against the registry and instantiates the matching
 * class. This is the single entry point for creating assemblers so that callers
 * never reference concrete `Type\*` classes directly.
 */
final class AssemblerFactory
{
	/**
	 * Stores the registry that maps assembler keys to their class names and
	 * the resolver that builds the mapped class through the container.
	 */
	public function __construct(
		private readonly AssemblerRegistry $assemblerRegistry,
		private readonly InstanceResolver  $resolver
	) {}

	/**
	 * Looks up the class registered for the given type and resolves a new
	 * instance from the container, forwarding `$params` as named constructor
	 * arguments. Accepts an `AssemblerType` case, one of the concrete `Type\`
	 * class names it defines, or a string key (built-in or custom). Returns
	 * `null` when no assembler is registered under the resolved key.
	 */
	public function make(AssemblerType|string $type, array $params = []): ?Assembler
	{
		$key = AssemblerType::key($type);

		/** @var null|class-string<Assembler> $assembler */
		if ($assembler = $this->assemblerRegistry->get($key)) {
			return $this->resolver->make($assembler, $params);
		}

		return null;
	}
}
