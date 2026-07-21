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
 * Builds markup formats from the container tag and enumerates the available set.
 * Unlike the query, assembler, and crumb factories (which resolve a key to a
 * container alias), this is injected with two maps derived from the same tag
 * and `slug` attribute: a `slug => class-string` map for enumeration, and a
 * `slug => Closure` map of deferred resolvers for building. Neither requires
 * a class to report its own key — the slug lives entirely in the tag
 * attribute recorded at registration, not in the class. That keeps both
 * building a format by slug and enumerating the formats for the block editor
 * driven by one source, and it is open to third parties: an extension that
 * tags its class under `Markup::TAG` with a `slug` is built and listed
 * alongside the built-ins. The built-ins are tagged from `MarkupType` by
 * `MarkupServiceProvider`.
 */
final class MarkupFactory
{
	/**
	 * Stores the `slug => class-string` map and the `slug => Closure` map of
	 * deferred resolvers, both derived from the tagged classes' `slug`
	 * attribute. Each closure builds its markup on demand, forwarding the
	 * params it is called with.
	 *
	 * @param array<string, Closure> $factories
	 */
	public function __construct(
		#[DeferTaggedWith(Markup::TAG, 'slug')] private readonly array $factories,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the markup registered for the given type, forwarding `$params` as
	 * named constructor arguments. Accepts any `MarkupType` (the `MarkupType`
	 * enum or a third-party implementation) or a string key. Returns `null` when
	 * no tagged markup type reports the key.
	 */
	public function make(MarkupDefinition|string $abstract, array $params = []): ?Markup
	{
		$abstract = is_string($abstract) ? $abstract : $abstract->value;

		// If passing a class string, we can just resolve directly.
		if (is_subclass_of($abstract, Markup::class)) {
			/** @var null|Markup */
			return $this->resolver->make($abstract, $params);
		}

		/** @var null|Markup */
		return isset($this->factories[$abstract]) ? ($this->factories[$abstract])($params) : null;
	}
}
