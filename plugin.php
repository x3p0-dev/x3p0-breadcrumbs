<?php
/**
 * Plugin Name:       X3P0: Breadcrumbs
 * Plugin URI:        https://github.com/x3p0-dev/x3p0-breadcrumbs
 * Description:       A breadcrumbs block for WordPress.
 * Version:           1.0.1
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Justin Tadlock
 * Author URI:        https://justintadlock.com
 * Text Domain:       x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

# Register autoloader for classes.
require_once 'src/Autoload.php';
Autoload::register();

# Load functions files.
require_once 'src/functions-helpers.php';

# Bootstrap the plugin.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\plugin', PHP_INT_MIN );
