<?php

/**
 * Block registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

use const X3P0\Breadcrumbs\PLUGIN_DIR;

/**
 * Registers the plugin's block types with WordPress from their built metadata
 * and adjusts that metadata at registration time so that rewrite-tag post
 * types map by default. Wired into WordPress on boot.
 */
final class BlockRegistrar implements Bootable
{
	/**
	 * Absolute path to the folder holding the plugin's built block metadata.
	 */
	private const BLOCKS_DIR = PLUGIN_DIR . '/public/blocks';

	/**
	 * Filename of the generated blocks metadata manifest.
	 */
	private const MANIFEST_FILENAME = 'blocks-manifest.php';

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		// We need to register this block very late so that we have
		// access to post types with the filter on the block type meta.
		add_action('init', $this->register(...), 999);
		add_filter('block_type_metadata', $this->setMetadata(...));
	}

	/**
	 * Registers the plugin's block types from their metadata collection.
	 */
	private function register(): void
	{
		wp_register_block_types_from_metadata_collection(
			self::BLOCKS_DIR,
			self::BLOCKS_DIR . '/' . self::MANIFEST_FILENAME
		);
	}

	/**
	 * Dynamically sets all publicly queryable post types with a `%tagname%`
	 * (rewrite tag) in their slug to have mapping enabled by default. This
	 * happens at the time of block registration.
	 */
	private function setMetadata(array $metadata): array
	{
		if ('x3p0/breadcrumbs' !== $metadata['name']) {
			return $metadata;
		}

		$types = array_filter(
			get_post_types(['publicly_queryable' => true], 'objects'),
			fn($type) => is_array($type->rewrite) && str_contains($type->rewrite['slug'] ?? '', '%')
		);

		$metadata['attributes']['mapRewriteTags']['default'] = [
			...array_fill_keys(array_keys($types), true),
			...$metadata['attributes']['mapRewriteTags']['default']
		];

		return $metadata;
	}
}
