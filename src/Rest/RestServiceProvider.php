<?php

/**
 * REST API service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Rest;

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires up the REST subsystem by booting the registrar that adds the
 * plugin's custom REST fields for use in the block editor.
 */
class RestServiceProvider extends ServiceProvider implements Bootable
{
	protected const BOOTABLE = [
		RestRegistrar::class
	];
}
