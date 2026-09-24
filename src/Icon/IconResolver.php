<?php

/**
 * Icon resolver class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * Resolves an icon based on the given preset definition/key passed into the
 * `resolve()` method.
 */
final class IconResolver
{
	/**
	 * Stores the presets and the choices laid over them.
	 */
	public function __construct(
		private readonly IconPresets $presets,
		private readonly IconConfig  $config = new IconConfig()
	) {}

	/**
	 * Returns the icon value in effect — a built-in text/glyph key or a
	 * `{collection}/{name}` reference — in descending order of how
	 * deliberate each source is:
	 *
	 * 1. The icon the caller configured for the key.
	 * 2. The icon the key is preset to.
	 * 3. The fallback, so a key nothing answers for still renders something.
	 */
	public function resolve(IconPresetDefinition|string $key): string
	{
		$key = IconPresetKey::normalize($key);

		if ('' !== ($icon = $this->config->get($key))) {
			return $icon;
		}

		if ('' !== ($icon = (string) $this->presets->get($key)?->icon)) {
			return $icon;
		}

		return (string) $this->presets->get(IconPresetKey::Fallback)?->icon;
	}
}
