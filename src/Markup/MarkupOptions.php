<?php

/**
 * Markup options class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\TaggedAbstractsWith;

/**
 * Derives option lists of markup types from the classes tagged under
 * `Markup::TAG` — the authoritative list of available types, including
 * third-party registrations — for the various contexts that need to present
 * them. Each method filters and shapes the available types for a specific
 * consumer.
 */
final class MarkupOptions
{
	/**
	 * Stores the factory the options are derived from.
	 */
	public function __construct(
		#[TaggedAbstractsWith(Markup::class, 'slug')] private readonly array $types
	) {}

	/**
	 * Returns the markup types offered as a block option — those whose class
	 * opts in by implementing `MarkupBlockOption` — as `key`/`name` pairs in
	 * tag order. This is the single source consumed by both the block's
	 * attribute `enum` and the editor script.
	 *
	 * @return array<int, array{key: string, name: string}>
	 */
	public function forBlock(): array
	{
		$options = [];

		foreach ($this->types as $attribute => $abstract) {
			if (is_subclass_of($abstract, MarkupBlockOption::class)) {
				$options[] = [
					'key'  => $attribute,
					'name' => $abstract::label()
				];
			}
		}

		return $options;
	}
}
