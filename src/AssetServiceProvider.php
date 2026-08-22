<?php

/**
 * Asset service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Packages\Asset\AssetResolver;
use X3P0\Breadcrumbs\Packages\Asset\PluginAssetResolver;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the framework-agnostic asset library into the plugin's container. One
 * shared `PluginAssetResolver`, anchored to the plugin's main file, answers for
 * every bundled file the plugin ships: anything that needs a URL, a filesystem
 * path, or the dependencies and build hash `@wordpress/scripts` wrote beside a
 * built entry point asks it, rather than assembling the path itself.
 *
 * It is bound in a `register()` factory rather than the `SINGLETONS_IF` map
 * because `PluginAssetResolver` takes the plugin file as a constructor string,
 * which the container cannot autowire. `singletonIf` still lets an extension
 * swap the resolver — for a decorated one, or a CDN-backed one — by binding
 * `AssetResolver` before this runs.
 *
 * The block assets are not resolved here. WordPress registers those from the
 * block metadata, which names its own files and resolves them itself.
 */
final class AssetServiceProvider extends ServiceProvider
{
	/**
	 * Binds the one resolver the plugin's assets are minted from as an
	 * overridable default.
	 */
	public function register(): void
	{
		$this->container->singletonIf(
			AssetResolver::class,
			static fn (): AssetResolver => new PluginAssetResolver(PLUGIN_FILE)
		);
	}
}
