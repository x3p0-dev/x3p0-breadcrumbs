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

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\DeferredTaggedMap;
use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Resolves a crumb type to a container identifier and builds it. A `CrumbType`
 * (the `CrumbType` enum or a third-party implementation) or its string key
 * resolves to the crumb's container alias and becomes the crumb's type slug; a
 * class name is built directly, and `getType()` derives its slug from the class.
 * Returns `null` for an unknown key so callers can dispatch optimistically.
 */
final class CrumbFactory
{
	/**
	 * Stores the resolver that builds the mapped class through the container.
	 */
	public function __construct(
		#[DeferredTaggedMap(Crumb::TAG, 'slug')] private readonly array $factories,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the crumb for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 */
	public function make(CrumbDefinition|string $abstract, array $params = []): ?Crumb
	{
		$abstract = is_string($abstract) ? $abstract : $abstract->value;

		// If passing a class string, we can just resolve directly.
		if (is_subclass_of($abstract, Crumb::class)) {
			/** @var null|Crumb */
			return $this->resolver->make($abstract, $params);
		}

		/** @var null|Crumb */
		return isset($this->factories[$abstract]) ? ($this->factories[$abstract])($params) : null;
	}
}
