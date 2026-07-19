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
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the crumb for the given type, forwarding `$params` as named
	 * constructor arguments and assigning its type slug from an alias key, or
	 * returns `null` when the type is unknown.
	 */
	public function make(CrumbType|string $type, array $params = []): ?Crumb
	{
		$abstract = is_string($type) ? $type : $type->classname();
		$abstract = class_exists($abstract) ? $abstract : CrumbType::tryFrom($abstract)?->classname();

		if (! $abstract) {
			return null;
		}

		/** @var Crumb $crumb */
		$crumb = $this->resolver->make($abstract, $params);

		if (is_string($type) && ! class_exists($type)) {
			$crumb->setType($type);
		} elseif ($type instanceof CrumbType) {
			$crumb->setType($type->value);
		}

		return $crumb;
	}
}
