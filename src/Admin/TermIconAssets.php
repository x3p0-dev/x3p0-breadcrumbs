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

use const X3P0\Breadcrumbs\PLUGIN_DIR;
use const X3P0\Breadcrumbs\PLUGIN_FILE;

/**
 * Loads the script and styles behind {@see TermIconField}, which is the whole
 * of the plugin's UI on the term editing screens.
 *
 * Unlike {@see EditorAssets}, this hooks nothing and boots nothing: whether the
 * assets are wanted is a question about the screen and the taxonomy on it, and
 * the field has to answer that anyway to decide whether there is anything to
 * render. Asking it twice would leave two copies of the same rule free to
 * drift, so the field calls {@see self::enqueue()} once it has settled the
 * matter — which also keeps the built asset file out of the way of every other
 * admin page, rather than requiring it on all of them to register a handle
 * almost none of them use.
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
	 * Path to the built assets, relative to the plugin folder. The screen
	 * they belong to is named in the path because the admin is not one
	 * surface: anything the plugin later adds to a different screen is a
	 * different bundle, and `public/admin/index.js` would have claimed the
	 * whole of it for this one field.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const ASSET_PATH = 'public/admin/edit-term';

	/**
	 * Enqueues the script and its styles. Call from a hook that runs within
	 * `admin_enqueue_scripts` — the caller knows which screens want them;
	 * this only knows what they are.
	 */
	public function enqueue(): void
	{
		$asset = require PLUGIN_DIR . '/' . self::ASSET_PATH . '/index.asset.php';

		wp_enqueue_script(
			self::HANDLE,
			$this->assetUrl('index.js'),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations(self::HANDLE, 'x3p0-breadcrumbs');

		// The block editor brings the component library's styles along for
		// its own UI, so the editor bundle can take them for granted. The
		// taxonomy screens are plain admin pages that load none of it, and
		// the icon library modal is unusable without them.
		wp_enqueue_style(
			self::HANDLE,
			$this->assetUrl('index.css'),
			['wp-components'],
			$asset['version']
		);

		// The build writes a flipped stylesheet beside the original, which
		// block registration would have picked up on its own. A hand-enqueued
		// style has to be told where it is.
		wp_style_add_data(self::HANDLE, 'rtl', 'replace');
	}

	/**
	 * Returns the URL to one of the built assets.
	 */
	private function assetUrl(string $file): string
	{
		return plugin_dir_url(PLUGIN_FILE) . self::ASSET_PATH . '/' . $file;
	}
}
