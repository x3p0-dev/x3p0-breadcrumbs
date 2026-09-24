<?php

/**
 * Icon presets class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use InvalidArgumentException;

/**
 * The single registry of the icon system: one {@see IconPreset} per icon
 * preset key, saying what the key shows and, when it carries a label, that
 * the block editor offers a control for it. Everything reads from here —
 * `IconResolver` for the icon, `IconOptions` for the controls — so an
 * extension says what it has to say about a key once.
 *
 * It stores presets and knows nothing about building them. Every key the
 * plugin owns, plus one per viewable post type, post type archive, and
 * taxonomy, is registered by `IconPresetRegistrar` on `init`, before
 * `IconPresetsRegistered` gives extensions their turn. A key nobody
 * registered has no preset, and `IconResolver` falls through to the trail's
 * fallback.
 */
final class IconPresets
{
	/**
	 * Registered presets, by preset key, in registration order.
	 *
	 * @var array<string, IconPreset>
	 */
	private array $presets = [];

	/**
	 * Amendments applied to a key's preset on the way out, by preset key.
	 *
	 * @var array<string, callable(IconPreset): IconPreset>
	 */
	private array $amendments = [];

	/**
	 * Headings declared for groups of an extension's own, by group key.
	 *
	 * @var array<string, string>
	 */
	private array $groups = [];

	/**
	 * Registers the preset for a key nothing else holds. Registering a key
	 * twice is a mistake rather than an override, so it throws; to change
	 * one, use `amend()`.
	 *
	 * @throws InvalidArgumentException If the key is already registered.
	 */
	public function register(IconPresetDefinition|string $key, IconPreset $preset): void
	{
		$key = IconPresetKey::normalize($key);

		if (isset($this->presets[$key])) {
			throw new InvalidArgumentException(sprintf(
				'An icon preset for "%s" is already registered. Use amend() to change it.',
				esc_html($key)
			));
		}

		$this->presets[IconPresetKey::normalize($key)] = $preset;
	}

	/**
	 * Amends a key's preset. The callback receives it and returns a copy, so
	 * it pairs with the `with*()` methods:
	 *
	 *     $presets->amend($key, fn (IconPreset $p) => $p->withGroup('my-group'));
	 *
	 * This is how an extension retargets a preset it doesn't own — the icon
	 * or grouping of a post type or taxonomy, say — without having to know
	 * whether anything is registered for it yet. A key with no preset is
	 * passed over, so amending a taxonomy a given site may not have needs no
	 * check first. Amendments compose: two of them for the same key both
	 * apply, in the order they were registered.
	 *
	 * @param callable(IconPreset): IconPreset $callback
	 */
	public function amend(IconPresetDefinition|string $key, callable $callback): void
	{
		$key      = IconPresetKey::normalize($key);
		$previous = $this->amendments[$key] ?? null;

		$this->amendments[$key] = $previous
			? static fn (IconPreset $preset) => $callback($previous($preset))
			: $callback;
	}

	/**
	 * Removes the preset registered for a key, if there is one.
	 */
	public function unregister(IconPresetDefinition|string $key): void
	{
		unset($this->presets[IconPresetKey::normalize($key)]);
	}

	/**
	 * Determines whether a preset is registered for the key.
	 */
	public function has(IconPresetDefinition|string $key): bool
	{
		return isset($this->presets[IconPresetKey::normalize($key)]);
	}

	/**
	 * Returns the preset in effect for the key, or `null` when nothing is
	 * registered for it. Amendments are applied on the way out.
	 */
	public function get(IconPresetDefinition|string $key): ?IconPreset
	{
		$key = IconPresetKey::normalize($key);

		if (! isset($this->presets[$key])) {
			return null;
		}

		return isset($this->amendments[$key])
			? ($this->amendments[$key])($this->presets[$key])
			: $this->presets[$key];
	}

	/**
	 * Returns every preset by key, in registration order.
	 *
	 * @return array<string, IconPreset>
	 */
	public function all(): array
	{
		$presets = [];

		foreach (array_keys($this->presets) as $key) {
			$presets[$key] = $this->get($key);
		}

		return $presets;
	}

	/**
	 * Declares the heading for a group of an extension's own. Groups are
	 * listed with the built-ins first, then these in declaration order.
	 */
	public function registerGroup(IconGroup|string $group, string $label): void
	{
		$this->groups[IconGroup::normalize($group)] = $label;
	}

	/**
	 * Returns every group heading by key: the built-ins in declaration
	 * order, then any an extension declared.
	 *
	 * @return array<string, string>
	 */
	public function groups(): array
	{
		$groups = [];

		foreach (IconGroup::cases() as $case) {
			$groups[$case->value] = $case->label();
		}

		return array_merge($groups, $this->groups);
	}
}
