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

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\DeferTagged;
use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Builds a `Crumb` instance from a type identifier, by invoking a factory
 * closure from the {@see Crumb::TAG} tag. Returns `null` when resolution is not
 * successful, so callers can dispatch optimistically.
 */
final class CrumbFactory
{
	/**
	 * Stores the resolver that builds the mapped class through the container.
	 */
	public function __construct(
		#[DeferTagged(Crumb::TAG)]
		private readonly array            $tagged,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the crumb for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 *
	 * The type be constructed via a class-string or an enum that implements
	 * the {@see CrumbDefinition} interface (the class can be derived from
	 * the enum).
	 */
	public function make(CrumbDefinition|string $type, array $params = []): ?Crumb
	{
		// If a plain string, assume it is the FQCN.
		$type = is_string($type) ? $type : $type->className();

		// If passing a class string, we can just resolve directly.
		if (is_subclass_of($type, Crumb::class)) {
			/** @var Crumb */
			return $this->resolver->make($type, $params);
		}

		/** @var null|Crumb */
		return isset($this->tagged[$type]) ? ($this->tagged[$type])($params) : null;
	}
}
