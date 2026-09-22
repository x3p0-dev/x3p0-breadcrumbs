<?php

/**
 * Icon option group registry class.
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
 * Registry of the groups the block editor sorts its icon option controls into,
 * keyed by group key. It stores groups and knows nothing about building them.
 * The built-ins are seeded by `IconOptionRegistrar` late on `init`, which then
 * dispatches `IconOptionsRegistering` so an extension with a family of options
 * of its own can register a group for them rather than scattering them through
 * the catch-all. Groups are listed in the editor in registration order, so an
 * extension's own group lands after the built-ins.
 *
 * @implements IteratorAggregate<string, IconOptionGroup>
 */
final class IconOptionGroupRegistry implements IteratorAggregate, Countable
{
	/**
	 * Stores the registered groups by key.
	 *
	 * @var array<string, IconOptionGroup>
	 */
	private array $groups = [];

	/**
	 * Registers a group under a key nothing else holds. Registering a key
	 * twice is a mistake rather than an override, so it throws; to change a
	 * registered group, use `replace()`.
	 *
	 * @throws InvalidArgumentException If the key is already registered.
	 */
	public function register(IconOptionGroupKey|string $key, IconOptionGroup $group): void
	{
		$key = IconOptionGroupKey::normalize($key);

		if (isset($this->groups[$key])) {
			throw new InvalidArgumentException(sprintf(
				'Icon option group "%s" is already registered. Use replace() to change it.',
				esc_html($key)
			));
		}

		$this->groups[$key] = $group;
	}

	/**
	 * Replaces the group registered under the key, keeping its original
	 * position.
	 *
	 * @throws InvalidArgumentException If the key is not registered.
	 */
	public function replace(IconOptionGroupKey|string $key, IconOptionGroup $group): void
	{
		$key = IconOptionGroupKey::normalize($key);

		if (! isset($this->groups[$key])) {
			throw new InvalidArgumentException(sprintf(
				'Icon option group "%s" is not registered. Use register() to add it.',
				esc_html($key)
			));
		}

		$this->groups[$key] = $group;
	}

	/**
	 * Removes the group registered under the key, if there is one. Options
	 * naming a group nobody registered are listed under the catch-all.
	 */
	public function unregister(IconOptionGroupKey|string $key): void
	{
		unset($this->groups[IconOptionGroupKey::normalize($key)]);
	}

	/**
	 * Determines whether a group is registered for the given key.
	 */
	public function has(IconOptionGroupKey|string $key): bool
	{
		return isset($this->groups[IconOptionGroupKey::normalize($key)]);
	}

	/**
	 * Returns the group registered for the given key, or `null` if none is.
	 */
	public function get(IconOptionGroupKey|string $key): ?IconOptionGroup
	{
		return $this->groups[IconOptionGroupKey::normalize($key)] ?? null;
	}

	/**
	 * Returns every registered group, keyed by group key, in registration
	 * order.
	 *
	 * @return array<string, IconOptionGroup>
	 */
	public function all(): array
	{
		return $this->groups;
	}

	/**
	 * @inheritDoc
	 */
	public function getIterator(): ArrayIterator
	{
		return new ArrayIterator($this->groups);
	}

	/**
	 * @inheritDoc
	 */
	public function count(): int
	{
		return count($this->groups);
	}
}
