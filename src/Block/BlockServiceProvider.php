<?php

/**
 * Block service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Block\Type\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\ServiceProvider;

final class BlockServiceProvider extends ServiceProvider implements Bootable
{
	/**
	 * Stores the path to the plugins blocks directory.
	 */
	private const BLOCKS_PATH = __DIR__ . '/../../public/blocks';

	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		$this->container->singleton(BlockRegistrar::class);
		$this->container->transient(Breadcrumbs::class);
	}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		$this->container->get(BlockRegistrar::class, [
			'path' => self::BLOCKS_PATH,
		])->boot();
	}
}
