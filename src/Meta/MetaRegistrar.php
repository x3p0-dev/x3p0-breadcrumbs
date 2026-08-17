<?php

/**
 * Meta registrar.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Meta;

use WP_Icons_Registry;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Registers the plugin's post/term meta keys with WordPress by hooking
 * {@see self::register()} onto the `init` action.
 */
final class MetaRegistrar implements Bootable
{
	/**
	 * Entry point for the class. Call once during plugin bootstrap to hook
	 * meta registration into WordPress's `init` action.
	 */
	public function boot(): void
	{
		add_action('init', $this->register(...));
	}

	/**
	 * Registers {@see MetaKey::Icon} for both posts and terms, using an
	 * empty `$object_subtype` so it applies across every post type and
	 * taxonomy WordPress knows about — including ones registered after
	 * this call, since core resolves the empty subtype dynamically rather
	 * than snapshotting the registered types/taxonomies at call time.
	 */
	private function register(): void
	{
		$args = [
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => $this->sanitizeIcon(...)
		];

		register_post_meta('', MetaKey::Icon->value, $args);
		register_term_meta('', MetaKey::Icon->value, $args);
	}

	/**
	 * Sanitizes an icon meta value by whitelisting it against WordPress's
	 * icon registry: any value that is not a currently registered
	 * `{collection}/{name}` icon (including empty, malformed, or
	 * deregistered values) is rejected in favor of an empty string. Unlike
	 * `Icon\IconResolver`, which also resolves the plugin's built-in
	 * text/glyph icons and remaps deprecated keys for legacy block
	 * attributes, this meta is a fresh, user-authored value with no
	 * backward-compatibility baggage, so only real registered icon library
	 * references are accepted.
	 */
	private function sanitizeIcon(mixed $value): string
	{
		$value = sanitize_text_field((string) $value);

		return WP_Icons_Registry::get_instance()->is_registered($value) ? $value : '';
	}
}
