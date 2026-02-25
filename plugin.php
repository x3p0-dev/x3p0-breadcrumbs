<?php

/**
 * Plugin Name:       X3P0: Breadcrumbs
 * Plugin URI:        https://github.com/x3p0-dev/x3p0-breadcrumbs
 * Description:       A breadcrumbs block for WordPress.
 * Version:           4.1.0
 * Requires at least: 6.8
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
const PLUGIN_PATH  = __DIR__;

# Load the autoloader.
if (! class_exists(Plugin::class) && is_file(PLUGIN_PATH . '/vendor/autoload.php')) {
	require_once PLUGIN_PATH . '/vendor/autoload.php';
}

# Initialize the plugin.
add_action('plugins_loaded', plugin(...), 9999);

# Boot registered services.
add_action('plugins_loaded', fn() => plugin()->boot(), PHP_INT_MAX);
