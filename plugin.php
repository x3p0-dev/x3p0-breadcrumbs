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

# ------------------------------------------------------------------------------
# Run the Composer autoloader.
# ------------------------------------------------------------------------------
#
# Auto-load classes and files via the Composer autoloader. Be sure to check if
# the file exists in case someone's using Composer to load their dependencies in
# a different directory.

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

# ------------------------------------------------------------------------------
# Bootstrap plugin.
# ------------------------------------------------------------------------------
#
# Just runs a small bootstrapping routine.

app();
