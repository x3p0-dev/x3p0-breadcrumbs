<?php

/**
 * Filters blocks out of the inserter.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Hides the Core breadcrumb block from the inserter. The plugin owns the
 * breadcrumb experience, and users get confused when seeing two blocks with the
 * same name. So this block is removed from the picker while remaining
 * registered — existing instances keep rendering. In any active development
 * mode the blocks stay available for testing and comparison.
 */
final class BlockInserterFilter implements Bootable
{
	/**
	 * Block types hidden from the inserter.
	 *
	 * @var  array<string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const HIDDEN = ['core/breadcrumbs'];

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_filter('block_type_metadata', $this->hideFromInserter(...));
	}

	/**
	 * Disables inserter support for hidden blocks, except while a
	 * development mode is active.
	 */
	private function hideFromInserter(array $metadata): array
	{
		if (
			in_array($metadata['name'], self::HIDDEN, true)
			&& ! wp_get_development_mode()
		) {
			$metadata['supports']['inserter'] = false;
		}

		return $metadata;
	}
}
