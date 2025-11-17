<?php

/**
 * Plugin application class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerServiceProvider;
use X3P0\Breadcrumbs\Block\BlockServiceProvider;
use X3P0\Breadcrumbs\Crumb\CrumbServiceProvider;
use X3P0\Breadcrumbs\Framework\Core\Application;
use X3P0\Breadcrumbs\Markup\MarkupServiceProvider;
use X3P0\Breadcrumbs\Query\QueryServiceProvider;
use X3P0\Breadcrumbs\Rest\RestServiceProvider;

/**
 * The Plugin class is an implementation of the Application contract. It's used
 * to register the default service providers, bootstrapping the plugin.
 */
final class Plugin extends Application
{
	/**
	 * Defines the plugin's namespace, which is used as a hook prefix.
	 */
	protected const NAMESPACE = 'x3p0/breadcrumbs';

	/**
	 * Defines the plugin's default service providers.
	 */
	protected const PROVIDERS = [
		AssemblerServiceProvider::class,
		BreadcrumbsServiceProvider::class,
		BlockServiceProvider::class,
		CrumbServiceProvider::class,
		MarkupServiceProvider::class,
		QueryServiceProvider::class,
		RestServiceProvider::class
	];
}
