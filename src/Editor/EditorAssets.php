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
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

use const X3P0\Breadcrumbs\PLUGIN_DIR;
use const X3P0\Breadcrumbs\PLUGIN_FILE;

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
	 * Path to the built editor assets, relative to the plugin folder.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const ASSET_PATH = 'public/editor';

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
		if (! $this->isBlockEditorScreen()) {
			return;
		}

		$asset = require PLUGIN_DIR . '/' . self::ASSET_PATH . '/index.asset.php';

		wp_enqueue_script(
			self::HANDLE,
			$this->assetUrl('index.js'),
			$asset['dependencies'],
			$asset['version'],
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
			$this->assetUrl('index.css'),
			[],
			$asset['version']
		);

		// The build writes a flipped stylesheet beside the original, which
		// block registration would have picked up on its own. A hand-enqueued
		// style has to be told where it is.
		wp_style_add_data(self::HANDLE, 'rtl', 'replace');
	}

	/**
	 * Whether a block editor is being loaded, which is as much as this side can
	 * usefully say. The `enqueue_block_editor_assets` hook also fires for the
	 * widget screens, and there is no Summary panel there for the icon row to
	 * fill.
	 *
	 * Which editor it is, and what is being edited in it, is deliberately left
	 * to the script. A screen is the wrong thing to ask: the post editor and the
	 * site editor both show the Summary panel, but the site editor answers for
	 * the whole of itself, so by the time it is editing a page there is nothing
	 * here left to read. `IconRow` settles it against the post type the editor
	 * reports instead, which is true in either one.
	 */
	private function isBlockEditorScreen(): bool
	{
		return (bool) get_current_screen()?->is_block_editor();
	}

	/**
	 * Returns the URL to one of the built editor assets.
	 */
	private function assetUrl(string $file): string
	{
		return plugin_dir_url(PLUGIN_FILE) . self::ASSET_PATH . '/' . $file;
	}
}
