<?php

/**
 * Icon options registry class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * Registry of the available icon options, keyed by their option key. The
 * built-ins — including one option per viewable post type and public taxonomy
 * — are seeded by `IconOptionRegistrar` late on `init`; third-party code adds
 * (or retargets) options with the same single `add()` call, so there is one
 * mechanism for everyone. `add()` is last-write-wins, letting an extension
 * replace a built-in default by re-registering its key.
 */
final class IconOptionRegistry
{
	/**
	 * Stores the registered options by key.
	 *
	 * @var array<string, IconOption>
	 */
	private array $options = [];

	/**
	 * Adds one or more options to the registry. Re-adding an existing key
	 * overwrites that option in place, keeping its original position.
	 */
	public function add(IconOption ...$options): void
	{
		foreach ($options as $option) {
			$this->options[$option->key] = $option;
		}
	}

	/**
	 * Retargets the icon for the given key, carrying the registered option's
	 * label through unchanged, or registers an unlabeled option when the key
	 * is new. This is the partial override `add()` cannot express: `add()`
	 * replaces an option wholesale, which would drop a label the registrar
	 * derived from a post type or taxonomy object. Extensions retargeting a
	 * built-in default on `IconOptionsRegistered` want exactly this.
	 */
	public function setIcon(string $key, string $icon): void
	{
		$this->add(
			$this->get($key)?->with(['icon' => $icon])
				?? new IconOption($key, $icon)
		);
	}

	/**
	 * Determines whether an option is registered for the given key.
	 */
	public function has(string $key): bool
	{
		return isset($this->options[$key]);
	}

	/**
	 * Returns the option registered for the given key, or `null` if none is.
	 */
	public function get(string $key): ?IconOption
	{
		return $this->options[$key] ?? null;
	}

	/**
	 * Returns the default icon attribute value registered for the given key,
	 * or an empty string if the key has no option (or no default icon).
	 */
	public function icon(string $key): string
	{
		return $this->get($key)?->icon ?? '';
	}

	/**
	 * Returns the options offered as block editor controls — those with a
	 * label — as `key`/`icon`/`name` triples in registration order. This is
	 * the single source the editor script consumes; unlabeled options are
	 * default-carriers only and are omitted.
	 *
	 * @return array<int, array{key: string, icon: string, name: string}>
	 */
	public function forBlock(): array
	{
		$options = [];

		foreach ($this->options as $option) {
			if ('' !== $option->label) {
				$options[] = [
					'key'  => $option->key,
					'icon' => $option->icon,
					'name' => $option->label
				];
			}
		}

		return $options;
	}
}
