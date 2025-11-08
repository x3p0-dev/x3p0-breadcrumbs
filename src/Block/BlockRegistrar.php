<?php

/**
 * Block registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Contracts\Bootable;

final class BlockRegistrar implements Bootable
{
	/**
	 * Sets the path where the built blocks are stored.
	 */
	public function __construct(protected string $path)
	{}

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
	 * Registers the block with WordPress.
	 */
	public function register(): void
	{
		wp_register_block_types_from_metadata_collection(
			$this->path,
			"{$this->path}/manifest.php"
		);
	}

	/**
	 * Dynamically sets all publicly queryable post types with a `%tagname%`
	 * (rewrite tag) in their slug to have mapping enabled by default. This
	 * happens at the time of block registration.
	 */
	public function setMetadata(array $metadata): array
	{
		if ('x3p0/breadcrumbs' !== $metadata['name']) {
			return $metadata;
		}

		$types = array_filter(
			get_post_types(['publicly_queryable' => true], 'objects'),
			fn($type) => is_array($type->rewrite) && str_contains($type->rewrite['slug'] ?? '', '%')
		);

		$metadata['attributes']['mapRewriteTags'] = [
			'type'    => 'object',
			'default' => ['post' => true] + array_fill_keys(array_keys($types), true)
		];

		return $metadata;
	}
}
