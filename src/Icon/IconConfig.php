<?php

/**
 * Icon configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * The icons chosen for one trail, keyed by icon preset key. Holds overrides
 * only — what a key shows in the absence of one lives in {@see IconPresets},
 * which {@see IconResolver} pairs this with.
 *
 * A key that isn't named here carries no opinion and falls through to its
 * preset. The map is keyed by the raw string form because the same map arrives
 * from block attribute saved in post content.
 */
final class IconConfig
{
	/**
	 * Stores the chosen icons.
	 *
	 * @var array<string, string>
	 */
	private readonly array $icons;

	/**
	 * Sets up the config from a preset key → icon value map. Values may be
	 * given as {@see Icon} cases and are reduced to their reference; keys
	 * arrive as strings. Anything that isn't an icon is dropped rather than
	 * stored, since this map arrives off a block attribute.
	 *
	 * @param array<string, Icon|string> $icons
	 */
	public function __construct(array $icons = [])
	{
		$this->icons = array_filter(array_map(
			static fn (mixed $icon) => match (true) {
				$icon instanceof Icon => $icon->name(),
				is_string($icon)      => $icon,
				default               => ''
			},
			$icons
		));
	}

	/**
	 * Returns a copy of the config with the given key's icon set, taking
	 * the key and the icon in whichever form the caller has on hand.
	 */
	public function withIcon(IconPresetDefinition|string $key, Icon|string $icon): self
	{
		return new self(array_merge($this->icons, [
			IconPresetKey::normalize($key) => $icon
		]));
	}

	/**
	 * Returns the icon configured for the key, or an empty string when the
	 * caller has no opinion about it.
	 */
	public function get(IconPresetDefinition|string $key): string
	{
		return $this->icons[IconPresetKey::normalize($key)] ?? '';
	}
}
