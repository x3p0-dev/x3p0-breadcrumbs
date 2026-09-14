<?php

/**
 * Term icon assets class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Admin;

use X3P0\Breadcrumbs\Editor\EditorAssets;
use X3P0\Breadcrumbs\Packages\Asset\AssetResolver;

/**
 * Loads the script and styles behind {@see TermIconField}, which is the whole
 * of the plugin's UI on the term editing screens. This hooks nothing and boots
 * nothing: whether the assets are wanted is a question about the screen and the
 * taxonomy on it, and the field has to answer that anyway to decide whether
 * there is anything to render.
 */
final class TermIconAssets
{
	/**
	 * Script and style handle for the term editing screens' assets.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const HANDLE = 'x3p0-breadcrumbs-edit-term';

	/**
	 * Path to the built script, relative to the plugin folder.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const SCRIPT_PATH = 'public/js/edit-term.js';

	/**
	 * Path to the built stylesheet, relative to the plugin folder.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const STYLE_PATH = 'public/css/edit-term.css';

	/**
	 * Accepts the resolver the built files are minted from.
	 */
	public function __construct(private readonly AssetResolver $assets)
	{}

	/**
	 * Enqueues the script and its styles. Call from a hook that runs within
	 * `admin_enqueue_scripts` — the caller knows which screens want them;
	 * this only knows what they are.
	 */
	public function enqueue(): void
	{
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

		// The taxonomy screens are plain admin pages, so we need to
		// manually add `wp-components` as a dependency for the modal.
		wp_enqueue_style(
			self::HANDLE,
			$style->fileUrl(),
			['wp-components'],
			$style->version()
		);

		wp_style_add_data(self::HANDLE, 'rtl', 'replace');
	}
}
