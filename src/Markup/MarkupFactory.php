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

use Closure;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\DeferredTagged;

/**
 * Builds markup formats from the container tag and enumerates the available set.
 * Unlike the query, assembler, and crumb factories (which resolve a key to a
 * container alias), this is injected with one deferred resolver per tagged
 * markup class — keyed by class name — from which it derives a `key => class`
 * map (each class reports its own `key()`). That single source serves both
 * building a format by key and enumerating the formats for the block editor, and
 * it is open to third parties: an extension that tags its class under
 * `Markup::TAG` is built and listed alongside the built-ins. The built-ins are
 * tagged from `MarkupType` by `MarkupServiceProvider`.
 */
final class MarkupFactory
{
	/**
	 * Memoized `key => class-string` map, derived once from the tagged classes.
	 *
	 * @var  null|array<string, class-string<Markup>>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private ?array $classes = null;

	/**
	 * Stores the deferred resolvers for the tagged markup classes, one closure
	 * per class keyed by its class name. Each closure builds its markup on
	 * demand, forwarding the params it is called with.
	 *
	 * @param array<class-string<Markup>, Closure> $factories
	 */
	public function __construct(
		#[DeferredTagged(Markup::TAG)] private readonly array $factories
	) {}

	/**
	 * Builds the markup registered for the given type, forwarding `$params` as
	 * named constructor arguments. Accepts any `MarkupType` (the `MarkupType`
	 * enum or a third-party implementation) or a string key. Returns `null` when
	 * no tagged markup type reports the key.
	 */
	public function make(MarkupType|string $type, array $params = []): ?Markup
	{
		$key   = is_string($type) ? $type : $type->key();
		$class = $this->classes()[$key] ?? null;

		/** @var null|Markup */
		return $class ? ($this->factories[$class])($params) : null;
	}

	/**
	 * Returns the `key => class-string` map of every tagged markup type, in tag
	 * assignment order. The authoritative list of available types, including
	 * third-party registrations.
	 *
	 * @return array<string, class-string<Markup>>
	 */
	public function classes(): array
	{
		return $this->classes ??= array_combine(
			array_map(
				static fn (string $class): string => $class::type(),
				array_keys($this->factories)
			),
			array_keys($this->factories)
		);
	}
}
