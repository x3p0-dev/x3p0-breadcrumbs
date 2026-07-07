<?php

/**
 * Post type archives support class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

use WP_Post_Type;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\Singleton;

/**
 * Resolves post types by their archive slug, which `get_post_types()` cannot do
 * directly when `has_archive` is set as a string rather than a boolean. Marked
 * `#[Singleton]` so the container shares one instance per request and the
 * archive-slug map is built only once.
 */
#[Singleton]
final class PostTypeArchives
{
	/**
	 * Lazily built map of archive slug to the post type objects registered
	 * under it, cached for the duration of the request. Remains `null` until
	 * first built by `archiveSlugMap()`.
	 *
	 * @var null|array<string, WP_Post_Type[]>
	 */
	private ?array $postTypesBySlug = null;

	/**
	 * Gets post types by slug. This is needed because the `get_post_types()`
	 * function doesn't exactly match the `has_archive` argument when it's
	 * set as a string instead of a boolean.
	 *
	 * The archive-slug lookup is built once and cached, so repeated
	 * resolutions (e.g. walking a URL path segment by segment) don't rescan
	 * the post type registry each time.
	 *
	 * @return WP_Post_Type[]
	 */
	public function bySlug(string $slug): array
	{
		return $this->archiveSlugMap()[$slug] ?? [];
	}

	/**
	 * Builds (once) and returns the map of archive slug to the post type
	 * objects registered under it. A string `has_archive` is itself the
	 * archive slug; a boolean-true `has_archive` falls back to the rewrite
	 * slug. A `false` `has_archive` (or a disabled rewrite) contributes no
	 * entry.
	 *
	 * @return array<string, WP_Post_Type[]>
	 */
	private function archiveSlugMap(): array
	{
		if (null !== $this->postTypesBySlug) {
			return $this->postTypesBySlug;
		}

		$this->postTypesBySlug = [];

		foreach (get_post_types([], 'objects') as $type) {
			if (is_string($type->has_archive)) {
				$this->postTypesBySlug[$type->has_archive][] = $type;
			} elseif (
				true === $type->has_archive
				&& is_array($type->rewrite)
				&& isset($type->rewrite['slug'])
			) {
				$this->postTypesBySlug[$type->rewrite['slug']][] = $type;
			}
		}

		return $this->postTypesBySlug;
	}
}
