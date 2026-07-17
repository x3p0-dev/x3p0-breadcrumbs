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

use X3P0\Breadcrumbs\Packages\Framework\Container\InstanceResolver;

/**
 * Resolves a markup key to its registered class via the registry and
 * instantiates it. Returns `null` when the key is not registered.
 */
final class MarkupFactory
{
	/**
	 * Stores the registry used to look up markup classes by key and the
	 * resolver that builds the mapped class through the container.
	 */
	public function __construct(
		private readonly MarkupRegistry   $markupRegistry,
		private readonly InstanceResolver $resolver
	) {}

	/**
	 * Resolves the markup class registered for a given type from the
	 * container, forwarding `$params` as named constructor arguments.
	 * Accepts a `MarkupType` case, one of the concrete `Type\` class names
	 * it defines, or a string key (built-in or custom). Returns `null` for
	 * an unknown resolved key.
	 */
	public function make(MarkupType|string $type, array $params = []): ?Markup
	{
		$key = MarkupType::key($type);

		/** @var null|class-string<Markup> $markup */
		if ($markup = $this->markupRegistry->get($key)) {
			return $this->resolver->make($markup, $params);
		}

		return null;
	}
}
