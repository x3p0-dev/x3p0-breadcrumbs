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

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\TaggedAbstracts;
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
		#[TaggedAbstracts(Markup::TAG)]
		private readonly array            $tagged,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Builds the markup for the given type, forwarding `$params` as named
	 * constructor arguments, or returns `null` when the type is unknown.
	 *
	 * This can be constructed via an enum that implements the `MarkupDefinition`
	 * interface, a class-string, or a key value when the markup type
	 * implements {@see MarkupBlockOption}.
	 */
	public function make(MarkupDefinition|string $type, array $params = []): ?Markup
	{
		// Always use the classname from the interface because this
		// ensures it works in the `is_subclass_of()` check without
		// falling through to the tagged types.
		$className = is_string($type) ? $type : $type->className();

		// If this is not a markup class, assume it is a defined key and
		// attempt to look it up via the class's static `key()` method.
		if (! is_subclass_of($className, Markup::class)) {
			$className = $this->resolveByKey($className);

			if (! $className) {
				return null;
			}
		}

		/** @var Markup */
		return $this->resolver->make($className, $params);
	}

	/**
	 * Attempts to resolve a classname by its key.
	 *
	 * @param string $slug
	 * @return null|class-string<Markup>
	 */
	private function resolveByKey(string $slug): ?string
	{
		foreach ($this->tagged as $class) {
			if (
				is_subclass_of($class, MarkupBlockOption::class)
				&& $class::key() === $slug
			) {
				return $class;
			}
		}

		return null;
	}
}
