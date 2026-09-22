<?php

/**
 * Icon option class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * A named icon slot: the unit of icon configuration, defaults, and UI.
 * Immutable: to change part of a registered option, take a copy through one
 * of the `with*()` methods and hand it to `IconOptionRegistry::replace()`.
 */
final class IconOption
{
	/**
	 * The default icon attribute value, normalized to a string.
	 */
	public readonly string $icon;

	/**
	 * The group key, normalized to a string.
	 */
	public readonly string $group;

	/**
	 * Sets up the option. The `$icon` is the default rendered when the site
	 * owner hasn't chosen one: an {@see Icon} case for an icon this plugin
	 * ships, or a `{collection}/{name}` library reference for anyone else's.
	 * An option with a translated `$label` is offered as a block editor
	 * control; one without is a default-carrier only. The `$slug` names the
	 * WordPress object the option was derived from, if any, so the editor can
	 * tell apart two objects declaring the same label.
	 */
	public function __construct(
		Icon|string $icon = '',
		public readonly string $label = '',
		IconOptionGroupKey|string $group = IconOptionGroupKey::General,
		public readonly string $slug = ''
	) {
		$this->icon  = $icon instanceof Icon ? $icon->name() : $icon;
		$this->group = IconOptionGroupKey::normalize($group);
	}

	/**
	 * Returns a copy of the option with a different default icon.
	 */
	public function withIcon(Icon|string $icon): self
	{
		return new self($icon, $this->label, $this->group, $this->slug);
	}

	/**
	 * Returns a copy of the option with a different label. An empty label
	 * withdraws the option's block control, leaving it a default-carrier.
	 */
	public function withLabel(string $label): self
	{
		return new self($this->icon, $label, $this->group, $this->slug);
	}

	/**
	 * Returns a copy of the option listed under a different group.
	 */
	public function withGroup(IconOptionGroupKey|string $group): self
	{
		return new self($this->icon, $this->label, $group, $this->slug);
	}
}
