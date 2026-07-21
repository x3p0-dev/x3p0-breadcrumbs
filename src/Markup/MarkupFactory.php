<?php

/**
 * Markup factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\DeferTaggedWith;
use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Builds a `Markup` instance from a type identifier, either by instantiating
 * a class directly through the container or by invoking a factory closure
 * registered under a tagged slug. Returns `null` when resolution is not
 * successful, so callers can dispatch optimistically.
 */
final class MarkupFactory
{
	/**
	 * Stores the resolver that builds the mapped class through the container.
	 */
	public function __construct(
		#[DeferTaggedWith(Markup::TAG, 'slug')]
		private readonly array            $tagged,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the markup for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 *
	 * This can be constructed via an enum that implements the `MarkupDefinition`
	 * interface, a class-string, or a slug value when the markup type is
	 * tagged in the container via {@see Markup::TAG} with a valid `slug`
	 * value at the time of tagging {@see MarkupServiceProvider::register()}.
	 */
	public function make(MarkupDefinition|string $type, array $params = []): ?Markup
	{
		// Always use the classname from the interface because this
		// ensures it works in the `is_subclass_check()` without falling
		// through to the tagged types.
		$type = is_string($type) ? $type : $type->className();

		// If passing a class string, we can just resolve directly.
		if (is_subclass_of($type, Markup::class)) {
			/** @var Markup */
			return $this->resolver->make($type, $params);
		}

		/** @var null|Markup */
		return isset($this->tagged[$type]) ? ($this->tagged[$type])($params) : null;
	}
}
