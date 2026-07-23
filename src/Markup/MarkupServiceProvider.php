<?php

/**
 * Markup service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Packages\Framework\Container\Container;
use X3P0\Breadcrumbs\Packages\Framework\Container\ContainerException;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the markup subsystem into the container: binds the factory and options
 * as shared singletons (only if not already bound) and, from the `MarkupType`
 * enum as the source of truth, tags each built-in class under `Markup::TAG`
 * with its key as the `slug` attribute. `MarkupFactory` resolves a class-string
 * directly and a string key by looking it up among the tagged classes, which
 * stays open to third parties that tag their own classes under the same names.
 * The default block markup class is stored as a named container parameter
 * rather than baked into the `MarkupOptions` binding, so an extension can
 * override just that value via `setParam()` without needing to reconstruct
 * the rest of the binding.
 */
final class MarkupServiceProvider extends ServiceProvider
{
	/**
	 * The markup factory, bound as a shared singleton only if not already
	 * bound so extensions may replace it.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		MarkupFactory::class
	];

	/**
	 * The container parameter name under which the default block markup
	 * class is stored. An extension may override it via `setParam()` any
	 * time before `MarkupOptions` is first resolved.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const DEFAULT_BLOCK_CLASS_PARAM = 'x3p0/breadcrumbs/markup/default-block-class';

	/**
	 * Sets the default block markup class parameter, sets the markup options
	 * singleton, sets the contract for tagged markup types, and tags each
	 * built-in markup type to the {@see Markup::TAG} tag. The
	 * {@see MarkupType} enum is the source of the canonical markup types.
	 *
	 * @throws ContainerException
	 */
	public function register(): void
	{
		// The param representing the default markup type to use for the
		// block implementation of the breadcrumbs. If overwriting, this
		// must be a class string for a class that implements the
		// `MarkupBlockOption` interface.
		$this->container->setParam(
			self::DEFAULT_BLOCK_CLASS_PARAM,
			MarkupType::Rdfa->className()
		);

		// Defines the `Markup` class as the contract that all tagged
		// Markup types must follow.
		$this->container->setTagContract(Markup::TAG, Markup::class);

		// Define `MarkupOptions` as a singleton, passing in the tagged
		// markup types and the default type.
		$this->container->singletonIf(
			MarkupOptions::class,
			static fn (Container $container) => new MarkupOptions(
				$container->taggedAbstracts(Markup::TAG),
				(string) $container->getParam(self::DEFAULT_BLOCK_CLASS_PARAM)
			)
		);

		// Tag the canonical markup types.
		foreach (MarkupType::cases() as $type) {
			$this->container->tag($type->className(), Markup::TAG);
		}
	}
}
