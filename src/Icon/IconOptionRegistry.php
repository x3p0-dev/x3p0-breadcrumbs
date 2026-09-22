<?php

/**
 * Icon option registry class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Registry of the available icon options, keyed by option key. It stores
 * options and knows nothing about building them: callers construct an
 * {@see IconOption} and register it, or take a copy of a registered one and
 * replace it. The built-ins are seeded by `IconOptionRegistrar` late on
 * `init`, which then dispatches `IconOptionsRegistering` so third-party code
 * can register its own with the same calls. The groups the block editor sorts
 * these into live in their own {@see IconOptionGroupRegistry}.
 *
 * @implements IteratorAggregate<string, IconOption>
 */
final class IconOptionRegistry implements IteratorAggregate, Countable
{
	/**
	 * Stores the registered options by key.
	 *
	 * @var array<string, IconOption>
	 */
	private array $options = [];

	/**
	 * Registers an option under a key nothing else holds. Registering a key
	 * twice is a mistake rather than an override, so it throws; to change a
	 * registered option, use `replace()`.
	 *
	 * @throws InvalidArgumentException If the key is already registered.
	 */
	public function register(IconOptionKey|string $key, IconOption $option): void
	{
		$key = IconOptionKey::normalize($key);

		if (isset($this->options[$key])) {
			throw new InvalidArgumentException(sprintf(
				'Icon option "%s" is already registered. Use replace() to change it.',
				esc_html($key)
			));
		}

		$this->options[$key] = $option;
	}

	/**
	 * Replaces the option registered under the key, keeping its original
	 * position. Pairs with the option's `with*()` methods for changing one
	 * part of a built-in:
	 *
	 *     $options->replace($key, $options->get($key)->withIcon(Icon::Package));
	 *
	 * @throws InvalidArgumentException If the key is not registered.
	 */
	public function replace(IconOptionKey|string $key, IconOption $option): void
	{
		$key = IconOptionKey::normalize($key);

		if (! isset($this->options[$key])) {
			throw new InvalidArgumentException(sprintf(
				'Icon option "%s" is not registered. Use register() to add it.',
				esc_html($key)
			));
		}

		$this->options[$key] = $option;
	}

	/**
	 * Removes the option registered under the key, if there is one.
	 */
	public function unregister(IconOptionKey|string $key): void
	{
		unset($this->options[IconOptionKey::normalize($key)]);
	}

	/**
	 * Determines whether an option is registered for the given key.
	 */
	public function has(IconOptionKey|string $key): bool
	{
		return isset($this->options[IconOptionKey::normalize($key)]);
	}

	/**
	 * Returns the option registered for the given key, or `null` if none is.
	 */
	public function get(IconOptionKey|string $key): ?IconOption
	{
		return $this->options[IconOptionKey::normalize($key)] ?? null;
	}

	/**
	 * Returns every registered option, keyed by option key, in registration
	 * order.
	 *
	 * @return array<string, IconOption>
	 */
	public function all(): array
	{
		return $this->options;
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->options);
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int
	{
		return count($this->options);
	}
}
