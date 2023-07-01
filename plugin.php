<?php
/**
 * Plugin Name:       X3P0 - Breadcrumbs
 * Plugin URI:        https://github.com/x3p0-dev/x3p0-breadcrumbs
 * Description:       A breadcrumbs block for WordPress built from the robust Hybrid Breadcrumbs library.
 * Version:           1.0.0-beta-20210708
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * Author:            Justin Tadlock
 * Author URI:        https://x3p0.com
 * Text Domain:       x3p0-breadcrumbs
 * Domain Path:       /public/lang
 */

namespace X3P0\Breadcrumbs;

# Register autoloader for classes.
require_once 'src/Autoload.php';
Autoload::register();

# Load functions files.
require_once 'src/functions-helpers.php';

# Bootstrap the plugin.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\plugin', PHP_INT_MIN );
