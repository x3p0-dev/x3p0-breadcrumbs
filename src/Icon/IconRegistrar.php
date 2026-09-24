<?php

/**
 * Icon registrar.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Registers the plugin's {@see Icon} cases with WordPress's icon API on `init`.
 */
final class IconRegistrar implements Bootable
{
	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('init', $this->register(...));
	}

	/**
	 * Registers the icon collection, then every {@see Icon} case within it.
	 */
	private function register(): void
	{
		// Register the collection.
		wp_register_icon_collection(Icon::COLLECTION, [
			'label'       => __('Breadcrumbs', 'x3p0-breadcrumbs'),
			'description' => __('Icons bundled specifically for use in breadcrumbs.', 'x3p0-breadcrumbs')
		]);

		// Register icons for the collection.
		foreach (Icon::cases() as $icon) {
			wp_register_icon($icon->name(), [
				'label'     => $icon->label(),
				'file_path' => $icon->filePath()
			]);
		}
	}
}
