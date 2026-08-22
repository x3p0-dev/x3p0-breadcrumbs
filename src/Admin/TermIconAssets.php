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
	 * Path to the built script, relative to the plugin folder. The screen the
	 * bundle belongs to is named in the file because the admin is not one
	 * surface: anything the plugin later adds to a different screen is a
	 * different bundle, and `admin.js` would have claimed the whole of it for
	 * this one field.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const SCRIPT_PATH = 'public/js/edit-term.js';

	/**
	 * Path to the built stylesheet, relative to the plugin folder. It is its
	 * own build entry, so it carries its own `.asset.php` file and answers for
	 * its own dependencies and version.
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

		// The block editor brings the component library's styles along for
		// its own UI, so the editor bundle can take them for granted. The
		// taxonomy screens are plain admin pages that load none of it, and
		// the icon library modal is unusable without them.
		wp_enqueue_style(
			self::HANDLE,
			$style->fileUrl(),
			['wp-components'],
			$style->version()
		);

		// The build writes a flipped stylesheet beside the original, which
		// block registration would have picked up on its own. A hand-enqueued
		// style has to be told where it is.
		wp_style_add_data(self::HANDLE, 'rtl', 'replace');
	}
}
