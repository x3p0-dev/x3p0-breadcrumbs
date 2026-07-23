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
	 * Cached array of block options.
	 */
	private ?array $blockOptions = null;

	/**
	 * Stores the factory the options are derived from.
	 *
	 * @param class-string<Markup>[]          $types
	 * @param class-string<MarkupBlockOption> $defaultBlockClass
	 */
	public function __construct(
		private readonly array $types,
		private readonly string $defaultBlockClass
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
		if ($this->blockOptions !== null) {
			return $this->blockOptions;
		}

		$this->blockOptions = [];

		foreach ($this->types as $abstract) {
			if (is_subclass_of($abstract, MarkupBlockOption::class)) {
				$this->blockOptions[] = [
					'key'  => $abstract::key(),
					'name' => $abstract::label()
				];
			}
		}

		return $this->blockOptions;
	}
	/**
	 * Returns the key for the default block markup type. Falls back to the
	 * first available block option if the configured default isn't among
	 * the currently registered/tagged types.
	 */
	public function getBlockDefaultKey(): string
	{
		$default = $this->defaultBlockClass::key();

		foreach ($this->forBlock() as $option) {
			if ($option['key'] === $default) {
				return $default;
			}
		}

		return $this->forBlock()[0]['key'] ?? $default;
	}
}
