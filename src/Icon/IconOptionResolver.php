<?php

/**
 * Icon option resolver class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

/**
 * Resolves an icon option key to the icon attribute value in effect for one
 * render: the caller's choice for the key if there is one, else the registered
 * default. This is the single owner of that rule, so the markup layer asks it
 * for a crumb's icon and for its own (e.g., the separator) the same way. Built
 * per render by `BreadcrumbsRenderer` and handed to `Markup`; the value it
 * returns is what `IconRenderer` turns into markup.
 */
final class IconOptionResolver
{
	/**
	 * Stores the registered options and the caller's overrides.
	 */
	public function __construct(
		private readonly IconOptionRegistry $options,
		private readonly IconConfig         $config = new IconConfig()
	) {}

	/**
	 * Returns the icon attribute value for the given option key — a built-in
	 * text/glyph key or a `{collection}/{name}` icon library reference — in
	 * descending order of how deliberate each source is:
	 *
	 * 1. The icon the caller configured for the key.
	 * 2. The default registered for the key.
	 * 3. The default registered for {@see IconOptionKey::Fallback}, so a key
	 *    nobody registered still renders something.
	 *
	 * Returns an empty string only when none of those carries an icon.
	 */
	public function resolve(IconOptionKey|string $key): string
	{
		return $this->config->getIcon($key)
			?: $this->options->get($key)?->icon
			?: $this->options->get(IconOptionKey::Fallback)?->icon
			?: '';
	}
}
