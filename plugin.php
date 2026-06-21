<?php

/**
 * Plugin Name:       X3P0: Breadcrumbs
 * Plugin URI:        https://github.com/x3p0-dev/x3p0-breadcrumbs
 * Description:       A breadcrumbs block for WordPress.
 * Version:           5.0.0-alpha-1
 * Requires at least: 7.0
 * Requires PHP:      8.1
 * Author:            Justin Tadlock
 * Author URI:        https://justintadlock.com
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

# Prevent direct access.
defined('ABSPATH') || exit;

# Define the plugin constants.
const PLUGIN_DIR = __DIR__;

# Load the autoloader.
if (! class_exists(Plugin::class) && is_file(PLUGIN_DIR . '/vendor/autoload.php')) {
	require_once PLUGIN_DIR . '/vendor/autoload.php';
}

# Initialize the plugin and boot registered services.
add_action('plugins_loaded', function (): void {
	do_action('x3p0/breadcrumbs/register', plugin());
	plugin()->boot();
}, -999);
