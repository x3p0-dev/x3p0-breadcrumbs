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
	 */
	public function register(): void
	{
		wp_register_block_types_from_metadata_collection(
			$this->path,
			"{$this->path}/manifest.php"
		);
	}
}
