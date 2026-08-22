<?php

/**
 * Editor assets class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Editor;

use X3P0\Breadcrumbs\Block\BlockAssets;
use X3P0\Breadcrumbs\Meta\MetaKey;
use X3P0\Breadcrumbs\Packages\Asset\AssetResolver;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Loads the plugin's editor UI that isn't the block's own — currently the
 * breadcrumb icon row added to the Summary panel — and hands it the meta keys
 * it writes to, so the {@see MetaKey} enum stays the only place they are
 * spelled out. Wired into WordPress on boot.
 */
final class EditorAssets implements Bootable
{
	/**
	 * Script and style handle for the editor assets.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const HANDLE = 'x3p0-breadcrumbs-editor';

	/**
	 * JavaScript global that the editor data is assigned to, shared with the
	 * block editor assets ({@see BlockAssets}).
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const SCRIPT_GLOBAL = 'x3p0Breadcrumbs';

	/**
	 * Path to the built editor script, relative to the plugin folder.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const SCRIPT_PATH = 'public/js/editor.js';

	/**
	 * Path to the built editor stylesheet, relative to the plugin folder.
	 * It is its own build entry, so it carries its own `.asset.php` file and
	 * answers for its own dependencies and version.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const STYLE_PATH = 'public/css/editor.css';

	/**
	 * Accepts the resolver the built editor files are minted from.
	 */
	public function __construct(private readonly AssetResolver $assets)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('enqueue_block_editor_assets', $this->enqueue(...));
	}

	/**
	 * Enqueues the editor script and its styles, along with the meta keys the
	 * script writes to.
	 */
	private function enqueue(): void
	{
		if (! get_current_screen()?->is_block_editor()) {
			return;
		}

		$script = $this->assets->asset(self::SCRIPT_PATH);
		$style  = $this->assets->asset(self::STYLE_PATH);

		wp_enqueue_script(
			self::HANDLE,
			$script->fileUrl(),
			$script->dependencies(),
			$script->version(),
			true
		);

		wp_set_script_translations(self::HANDLE, 'x3p0-breadcrumbs');

		wp_add_inline_script(
			self::HANDLE,
			sprintf(
				'window.%1$s = Object.assign(window.%1$s || {}, %2$s);',
				self::SCRIPT_GLOBAL,
				wp_json_encode(
					[
						'metaKeys' => MetaKey::forEditor()
					],
					JSON_HEX_TAG | JSON_UNESCAPED_SLASHES
				)
			),
			'before'
		);

		wp_enqueue_style(
			self::HANDLE,
			$style->fileUrl(),
			$style->dependencies(),
			$style->version()
		);

		// The build writes a flipped stylesheet beside the original, which
		// block registration would have picked up on its own. A hand-enqueued
		// style has to be told where it is.
		wp_style_add_data(self::HANDLE, 'rtl', 'replace');
	}
}
