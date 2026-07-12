<?php

/**
 * Plugin application class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerServiceProvider;
use X3P0\Breadcrumbs\Block\BlockServiceProvider;
use X3P0\Breadcrumbs\Crumb\CrumbServiceProvider;
use X3P0\Breadcrumbs\Extension\ExtensionServiceProvider;
use X3P0\Breadcrumbs\Markup\MarkupServiceProvider;
use X3P0\Breadcrumbs\Packages\Framework\Core\Application;
use X3P0\Breadcrumbs\Query\QueryServiceProvider;
use X3P0\Breadcrumbs\Rest\RestServiceProvider;

/**
 * The plugin application. Extends the framework `Application` and bootstraps the
 * plugin by registering the service provider for each subsystem; each provider
 * wires its own bindings into the container and boots them as needed.
 */
final class Plugin extends Application
{
	/**
	 * The service providers registered on boot, one per subsystem.
	 *
	 * @var  array<string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const PROVIDERS = [
		AssemblerServiceProvider::class,
		BreadcrumbsServiceProvider::class,
		BlockServiceProvider::class,
		CrumbServiceProvider::class,
		EventServiceProvider::class,
		MarkupServiceProvider::class,
		QueryServiceProvider::class,
		RestServiceProvider::class,
		ExtensionServiceProvider::class
	];
}
