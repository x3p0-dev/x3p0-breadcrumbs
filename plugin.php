<?php
/**
 * Plugin Name: X3P0 - Breadcrumbs
 * Plugin URI:  https://x3p0.com
 * Description: Breadcrumbs in block form.
 * Version:     1.0.0
 * Author:      Justin Tadlock
 * Author URI:  https://x3p0.com
 * Text Domain: x3p0-breadcrumbs
 * Domain Path: /public/lang
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
