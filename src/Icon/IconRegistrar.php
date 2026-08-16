<?php

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Wires the plugin's {@see Icon} cases into WordPress's icon registry by
 * hooking {@see self::register()} onto the `init` action.
 */
final class IconRegistrar implements Bootable
{
	/**
	 * Entry point for the class. Call once during plugin bootstrap to hook
	 * icon registration into WordPress's `init` action.
	 */
	public function boot(): void
	{
		add_action('init', $this->register(...));
	}

	/**
	 * Registers the icon collection via `wp_register_icon_collection()`,
	 * then registers every case of {@see Icon} within it via
	 * `wp_register_icon()`, using each icon's handle, label, and file path.
	 */
	private function register(): void
	{
		// Register the collection.
		wp_register_icon_collection(Icon::COLLECTION, [
			'label'       => __('Breadcrumbs', 'x3p0-breadcrumbs'),
			'description' => __('TODO', 'x3p0-breadcrumbs')
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
