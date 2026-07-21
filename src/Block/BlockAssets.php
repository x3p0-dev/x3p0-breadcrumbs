<?php

/**
 * Block editor assets class.
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

/**
 * Passes server-side data to the block editor. The selectable markup types are
 * defined once in PHP (captured via {@see MarkupOptions}) and handed to the
 * editor script so the JavaScript never has to recreate (and risk desyncing)
 * the list.
 */
final class BlockAssets implements Bootable
{
	/**
	 * JavaScript global that the editor data is assigned to.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const SCRIPT_GLOBAL = 'x3p0Breadcrumbs';

	/**
	 * Stores the markup options passed to the editor.
	 */
	public function __construct(
		private readonly MarkupOptions $markupOptions
	) {}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('enqueue_block_editor_assets', $this->enqueue(...));
	}

	/**
	 * Attaches the editor data to the block's editor script. The handle is
	 * derived with `generate_block_asset_handle()` — the same function
	 * WordPress uses to build it from the block metadata — so it stays
	 * correct without hardcoding.
	 */
	private function enqueue(): void
	{
		wp_add_inline_script(
			generate_block_asset_handle(BlockRegistrar::BLOCK_NAME, 'editorScript'),
			sprintf(
				'window.%1$s = Object.assign(window.%1$s || {}, %2$s);',
				self::SCRIPT_GLOBAL,
				wp_json_encode(
					['markupTypes' => $this->markupOptions->forBlock()],
					JSON_HEX_TAG | JSON_UNESCAPED_SLASHES
				)
			),
			'before'
		);
	}
}
