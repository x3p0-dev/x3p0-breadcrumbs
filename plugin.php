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

use Hybrid\Breadcrumbs\Trail;
use Hybrid\Mix\Mix;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_action( 'init', __NAMESPACE__ . '\register' );

function register() {
	$path = trailingslashit( __DIR__ );
	$uri  = trailingslashit( plugins_url( '', __FILE__ ) );

	$mix = new Mix(
		"{$path}/public",
		"{$uri}/public"
	);

	wp_register_style(
		'x3p0-breadcrumbs',
		$mix->asset( 'css/style.css' ), null, null
	);

	wp_register_style(
		'x3p0-breadcrumbs-editor',
		$mix->asset( 'css/editor.css' ), null, null
	);

	wp_register_script(
		'x3p0-breadcrumbs-editor',
		$mix->asset( 'js/editor.js' ),
		[
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-i18n',
			'wp-element',
			'wp-server-side-render'
		],
		null,
		true
	);

	register_block_type( "{$path}/public", [
		'render_callback' => __NAMESPACE__ . '\render'
	] );
}

function render( $attr, $content, $block ) {

	$args = [
		'labels' => [ 'title' => '' ]
	];

	if ( isset( $attr['showOnHomepage'] ) ) {
		$args['show_on_front'] = $attr['showOnHomepage'];
	}

	if ( isset( $attr['showTrailEnd'] ) ) {
		$args['show_trail_end'] = $attr['showTrailEnd'];
	}

	$justify_class_name =
		empty( $attr['itemsJustification'] )
		? ''
		: "items-justified-{$attr['itemsJustification']}";

	$trail = Trail::render( $args );

	$wrapper_attributes = get_block_wrapper_attributes( [
		'class' => $justify_class_name
	] );

	return sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		$trail
	);
}
