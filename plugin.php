<?php

/**
 * Plugin Name:       X3P0: Breadcrumbs
 * Plugin URI:        https://github.com/x3p0-dev/x3p0-breadcrumbs
 * Description:       A breadcrumbs block for WordPress.
 * Version:           2.0.1
 * Requires at least: 6.6
 * Requires PHP:      8.0
 * Author:            Justin Tadlock
 * Author URI:        https://justintadlock.com
 * Text Domain:       x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

# Prevent direct access.
defined('ABSPATH') || exit;

# Register autoloader for classes.
require_once 'src/Autoload.php';
Autoload::register();

# Load functions files.
require_once 'src/functions-helpers.php';

# Bootstrap the plugin.
add_action('plugins_loaded', fn() => plugin()->boot(), PHP_INT_MIN);
