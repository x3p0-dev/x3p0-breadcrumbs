<?php

/**
 * Icon options class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The icon controls the block editor offers, shaped for the editor script.
 *
 * Read-only: there is nothing to register here. A key becomes an option by
 * carrying a label in its {@see IconPreset}, so everything this class lists
 * comes out of {@see IconPresets}. Each method shapes that list for a specific
 * consumer.
 */
final class IconOptions
{
	/**
	 * Stores the registry the controls are read from.
	 */
	public function __construct(private readonly IconPresets $presets)
	{}

	/**
	 * Returns the keys worth a control — those whose preset carries a label
	 * — shaped for the editor, each with the icon its key is currently
	 * preset to. A control naming a group nobody declared is filed under the
	 * `General` rather than disappearing.
	 *
	 * @return array<int, array{key: string, icon: string, name: string, group: string, slug: string}>
	 */
	public function forBlock(): array
	{
		$groups  = $this->presets->groups();
		$options = [];

		foreach ($this->presets->all() as $key => $preset) {
			if ('' === $preset->label) {
				continue;
			}

			$group = IconGroup::normalize($preset->group);

			$options[] = [
				'key'   => $key,
				'icon'  => $preset->icon,
				'name'  => $preset->label,
				'group' => isset($groups[$group]) ? $group : IconGroup::General->value,
				'slug'  => $preset->slug
			];
		}

		return $options;
	}

	/**
	 * Returns the group headings as `key`/`name` pairs in the order they
	 * should be listed, for the editor to lay its controls out under.
	 *
	 * @return array<int, array{key: string, name: string}>
	 */
	public function groupsForBlock(): array
	{
		$groups = [];

		foreach ($this->presets->groups() as $key => $label) {
			$groups[] = [
				'key'  => $key,
				'name' => $label
			];
		}

		return $groups;
	}

	/**
	 * Returns the icon preset key for each viewable post type's single-post
	 * crumbs, keyed by post type name, so the block canvas can preview the
	 * icon belonging to whatever post is open in the editor.
	 *
	 * @return array<string, string>
	 */
	public function postTypeKeys(): array
	{
		$postTypes = array_filter(
			get_post_types([], 'objects'),
			is_post_type_viewable(...)
		);

		return array_map(
			static fn ($type) => IconPresetKey::postType($type->name),
			$postTypes
		);
	}
}
