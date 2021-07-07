<?php
/**
 * Block class.
 *
 * Registers and renders the block type on the front end.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2021, Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs;

use Hybrid\Breadcrumbs\Trail;

/**
 * Handles the block type.
 *
 * @since  1.0.0
 * @access public
 */
class Block {

	/**
	 * Path to the `block.json` file.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $json_path = '';

	/**
	 * Sets up object state.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $json_path
	 * @return void
	 */
	public function __construct( string $json_path ) {
		$this->json_path = $json_path;
	}

	/**
	 * Boots the class, running its actions/filters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers the block with WordPress.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		register_block_type( $this->json_path, [
			'render_callback' => [ $this, 'render' ]
		] );
	}

	/**
	 * Registers assets with WordPress.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $attr
	 * @param  string  $content
	 * @return string
	 */
	public function render( $attr, $content ) {
		$args = [
			'labels'        => [ 'title' => '' ],
			'container_tag' => ''
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
			'role'       => 'navigation',
			'aria-label' => __( 'Breadcrumbs', 'x3p0-breadcrumbs' ),
			'itemprop'   => 'breadcrumb',
			'class'      => "breadcrumbs {$justify_class_name}"
		] );

		return sprintf(
			'<nav %1$s>%2$s</nav>',
			$wrapper_attributes,
			$trail
		);

	}
}
