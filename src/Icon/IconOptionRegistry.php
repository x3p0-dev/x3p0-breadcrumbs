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
 *
 * The registry also holds the groups the block editor sorts those options
 * into. A group is a key and a translated label; options name their group by
 * key. Extensions register groups of their own with the same `addGroup()` the
 * built-ins use, so an extension with a family of its own options can gather
 * them under its own heading instead of scattering them through the catch-all.
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
	 * Stores the registered group labels by group key, in the order the block
	 * editor lists them.
	 *
	 * @var array<string, string>
	 */
	private array $groups = [];

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
	 * Registers the translated label for a group of options, or relabels an
	 * existing group in place. Groups are listed in the block editor in the
	 * order they were first registered, which needs no explicit ordering to
	 * come out right: the built-ins are seeded before the
	 * `IconOptionsRegistered` event, so an extension's own group — a
	 * WooCommerce group holding its shop and endpoint options, say — lands
	 * after them.
	 */
	public function addGroup(string $key, string $label): void
	{
		$this->groups[$key] = $label;
	}

	/**
	 * Changes one or more parts of a registered option, leaving the rest as
	 * they are. This is the partial override `add()` cannot express: `add()`
	 * replaces an option wholesale, which would drop the label and slug the
	 * registrar derived from a post type or taxonomy object. Retargeting a
	 * built-in default, renaming an option to suit the vocabulary of the
	 * plugin that owns the thing it names, and gathering options into an
	 * extension's own group are all the same operation on different parts, so
	 * they share one method and read as what they are at the call site:
	 *
	 *     $options->update($key, icon: Icon::Package);
	 *     $options->update($key, label: __('Shop', 'my-plugin'));
	 *     $options->update($key, group: 'woocommerce');
	 *
	 * An argument left null is left alone. The icon is passed straight through
	 * to the option, so it takes an {@see Icon} case or a raw reference string
	 * on the same terms as the constructor.
	 *
	 * Updating a key nothing is registered under does nothing, deliberately:
	 * an extension speaking for objects that may or may not exist on a given
	 * site — WooCommerce naming product attribute taxonomies a store need not
	 * have — would otherwise conjure an option for a thing that isn't there.
	 * Registering is `add()`'s job.
	 */
	public function update(
		string $key,
		Icon|string|null $icon = null,
		?string $label = null,
		?string $group = null
	): void {
		$option = $this->get($key);

		if (null === $option) {
			return;
		}

		$this->add($option->with(array_filter([
			'icon'  => $icon,
			'label' => $label,
			'group' => $group
		], static fn ($value) => null !== $value)));
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
	 * label — in registration order. This is the single source the editor
	 * script consumes; unlabeled options are default-carriers only and are
	 * omitted. An option naming a group nobody registered falls back to the
	 * catch-all rather than disappearing, since the editor renders its rows
	 * group by group and would have nowhere to put it.
	 *
	 * @return array<int, array{key: string, icon: string, name: string, group: string, slug: string}>
	 */
	public function forBlock(): array
	{
		$options = [];

		foreach ($this->options as $option) {
			if ('' !== $option->label) {
				$options[] = [
					'key'   => $option->key,
					'icon'  => $option->icon,
					'name'  => $option->label,
					'group' => isset($this->groups[$option->group])
						? $option->group
						: IconOption::GROUP_GENERAL,
					'slug'  => $option->slug
				];
			}
		}

		return $options;
	}

	/**
	 * Returns the registered groups as `key`/`name` pairs in registration
	 * order, for the editor to lay its option controls out under. Groups with
	 * no labeled options in them are left for the editor to skip, since only
	 * it knows which options are still on offer at any moment.
	 *
	 * @return array<int, array{key: string, name: string}>
	 */
	public function groupsForBlock(): array
	{
		$groups = [];

		foreach ($this->groups as $key => $label) {
			$groups[] = [
				'key'  => $key,
				'name' => $label
			];
		}

		return $groups;
	}
}
