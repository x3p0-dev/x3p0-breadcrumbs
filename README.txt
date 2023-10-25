=== X3P0: Breadcrumbs ===

Contributors: greenshady
Donate link: https://themehybrid.com/donate
Tags: breadcrumbs, navigation, menu
Requires at least: 6.3
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Breadcrumbs block for WordPress that supports Schema.org HTML5-valid microdata.

== Description ==

X3P0 Breadcrumbs is one of the most advanced and robust breadcrumb navigation systems ever built for WordPress. It was born from a small script [first released in 2009](https://justintadlock.com/archives/2009/04/05/breadcrumb-trail-wordpress-plugin) and has grown into a system that can handle nearly any site's setup to show the most accurate breadcrumbs for each page.

### Blocks and Classic Support

The plugin works with both block and classic themes, so you can use it on any WordPress site.

For **block themes**, you can insert it into any template or template part via the WordPress Site Editor. Ideally, this would be something like the Header part, which is generally shown on every page of the site.

For **classic themes**, you must manually add the PHP code to your theme to call the breadcrumb trail like so:

	<?php if ( class_exists( '\X3P0\Breadcrumbs\Trail' ) ) {
		\X3P0\Breadcrumbs\Trail::display();
	} ?>

### How It Works

This plugin automatically detects your permalink setup and displays breadcrumbs based on that structure. Nearly all sites have some sort of hierarchy. The plugin recognizes that and builds a set of unique breadcrumbs for each page on your site.

This means that it can also detect custom post types and taxonomies right out of the box. Whatever you throw at it, it's got a solution.

== Frequently Asked Questions ==

### Can you add X, Y, or Z feature?

Feel free to open a ticket on the plugin's [GitHub repository](https://github.com/x3p0-dev/x3p0-breadcrumbs/issues). We'll chat about it. The PHP for the block is much more robust than what's currently controllable via the block editor, so it's likely the feature already exists---it just needs the editor UI component built out.

== Screenshots ==

1. Breadcrumbs block in the Site Editor.
2. Home icon picker.
3. Separator icon picker.

== Changelog ==

Please see the `changelog.md` file included with the plugin file.  Or, you can view the [online change log](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/CHANGELOG.md).
