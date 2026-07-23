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

use X3P0\Breadcrumbs\Markup\MarkupOptions;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

use const X3P0\Breadcrumbs\PLUGIN_DIR;

/**
 * Registers the plugin's block types with WordPress from their built metadata
 * and adjusts that metadata at registration time: rewrite-tag post types are
 * marked to map by default, and the `markup` attribute's accepted values are
 * synced from the registered markup options so the enum never drifts from the
 * registry. Wired into WordPress on boot.
 */
final class BlockRegistrar implements Bootable
{
	/**
	 * Name of the plugin's block type, matching the `name` in its
	 * `block.json` metadata.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const BLOCK_NAME = 'x3p0/breadcrumbs';

	/**
	 * Absolute path to the folder holding the plugin's built block metadata.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const BLOCKS_DIR = PLUGIN_DIR . '/public/blocks';

	/**
	 * Filename of the generated blocks metadata manifest.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const MANIFEST_FILENAME = 'blocks-manifest.php';

	/**
	 * Stores the markup options used to seed the block's `markup` attribute.
	 */
	public function __construct(
		private readonly MarkupOptions $markupOptions
	) {}

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
	 * Adjusts the block metadata at registration time. It enables mapping
	 * by default for all publicly queryable post types with a `%tagname%`
	 * (rewrite tag) in their slug, and derives the accepted `markup` values
	 * from the registered block options so they never drift from the registry.
	 */
	private function setMetadata(array $metadata): array
	{
		if (self::BLOCK_NAME !== $metadata['name']) {
			return $metadata;
		}

		// Assign the default markup based on what is registered for the
		// `MarkupBlockOption` interface.
		$metadata['attributes']['markup']['default'] = $this->markupOptions->getBlockDefaultKey();

		// Get the post types with a `%tag%` in their rewrite slug and
		// mark them to map rewrite tags by default.
		$types = array_filter(
			get_post_types(['publicly_queryable' => true], 'objects'),
			static fn($type) => is_array($type->rewrite) && str_contains($type->rewrite['slug'] ?? '', '%')
		);

		$metadata['attributes']['mapRewriteTags']['default'] = [
			...array_fill_keys(array_keys($types), true),
			...$metadata['attributes']['mapRewriteTags']['default']
		];

		// Keep the accepted markup values in sync with the registered types
		// offered as block options so the attribute enum, the editor options,
		// and the markup registry never drift apart.
		$metadata['attributes']['markup']['enum'] = array_column(
			$this->markupOptions->forBlock(),
			'key'
		);

		return $metadata;
	}
}
