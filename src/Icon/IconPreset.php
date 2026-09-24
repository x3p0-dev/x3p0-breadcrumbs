<?php

/**
 * Icon preset class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * Creates an icon preset with the icon it shows before anyone configures it
 * otherwise and, when the key is worth setting from the block editor, what that
 * control is called and where it is filed.
 *
 * Immutable. To change part of one — a group, a label — take a copy through
 * one of the `with*()` methods, which is what `IconPresets::amend()` expects
 * its callback to return.
 */
final class IconPreset
{
	/**
	 * The icon, normalized to a `{collection}/{name}` reference (or a
	 * built-in text/glyph key).
	 */
	public readonly string $icon;

	/**
	 * Sets up the preset. The `$icon` is what the key shows and is always
	 * required. It is an {@see Icon} case for an icon this plugin bundles,
	 * or a `{collection}/{name}` reference for anyone else's. A translated
	 * `$label` makes the key an option in the block editor. The `$group` is
	 * the heading the control is listed under, as an {@see IconGroup} case
	 * or the key of a registered group. The `$slug` names the WordPress
	 * object the preset was derived from, if any, so the editor can tell
	 * apart two objects declaring the same label.
	 */
	public function __construct(
		Icon|string $icon,
		public readonly string $label = '',
		public readonly IconGroup|string $group = IconGroup::General,
		public readonly string $slug = ''
	) {
		$this->icon = $icon instanceof Icon ? $icon->name() : $icon;
	}

	/**
	 * Returns a copy showing a different icon.
	 */
	public function withIcon(Icon|string $icon): self
	{
		return new self($icon, $this->label, $this->group, $this->slug);
	}

	/**
	 * Returns a copy under a different label. An empty label withdraws the
	 * key's control, leaving the preset to carry the icon alone.
	 */
	public function withLabel(string $label): self
	{
		return new self($this->icon, $label, $this->group, $this->slug);
	}

	/**
	 * Returns a copy filed under a different group, so an extension can
	 * gather the controls for the WordPress objects it owns under a heading
	 * of its own.
	 */
	public function withGroup(IconGroup|string $group): self
	{
		return new self($this->icon, $this->label, $group, $this->slug);
	}
}
