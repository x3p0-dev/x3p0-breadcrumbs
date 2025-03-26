<?php

/**
 * Block registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-ideas
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use DirectoryIterator;
use X3P0\Breadcrumbs\Contracts\Bootable;

class Register implements Bootable
{
	/**
	 * Sets the path where the built blocks are stored.
	 */
	public function __construct(protected string $path)
	{}

	/**
	 * {@inheritdoc}
	 */
	public function boot(): void
	{
		add_action('init', [ $this, 'register' ]);
	}

	/**
	 * Registers the block with WordPress.
	 *
	 * @internal WordPress hook callback. Do not call directly.
	 * @todo     Remove `function_exists()` with minimum WP 6.7 support.
	 */
	public function register(): void
	{
		// WordPress 6.8.
		if (
			function_exists('wp_register_block_types_from_metadata_collection')
			&& file_exists("{$this->path}/manifest.php")
		) {
			wp_register_block_types_from_metadata_collection(
				$this->path,
				"{$this->path}/manifest.php"
			);

			return;
		}

		// WordPress 6.7.
		if (
			function_exists('wp_register_block_metadata_collection')
			&& file_exists("{$this->path}/manifest.php")
		) {
			wp_register_block_metadata_collection(
				$this->path,
				"{$this->path}/manifest.php"
			);
		}

		foreach (new DirectoryIterator($this->path) as $dir) {
			if ($dir->isDir() && ! $dir->isDot()) {
				register_block_type($dir->getRealPath());
			}
		}
	}
}
