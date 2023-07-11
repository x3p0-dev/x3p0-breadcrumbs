<?php
/**
 * Block class registers and renders the block type on the front end.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

use WP_Block;
use X3P0\Breadcrumbs\Contracts\Bootable;

class Block implements Bootable
{
	/**
	 * Stores the plugin path.
	 *
	 * @since 1.0.0
 	 * @todo  Move this to the constructor with PHP 8-only support.
	 */
	protected string $path;

        /**
         * Sets up object state.
         *
         * @since 1.0.0
         */
        public function __construct( string $path )
	{
		$this->path = $path;
	}

	/**
	 * Boots the class, running its actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function boot(): void
	{
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers the block with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
                register_block_type( $this->path . '/public', [
                        'render_callback' => [ $this, 'render' ]
                ] );
	}

	/**
	 * Renders the block on the front end.
	 *
	 * @since 1.0.0
	 */
        public function render( array $attr ): string
        {
		$args = [
			'labels'             => [ 'title' => '' ],
			'container_tag'      => '',
			'post_taxonomy'      => [ 'post' => 'category' ],
			'post_rewrite_tags'  => false
		];

		if ( isset( $attr['showOnHomepage'] ) ) {
			$args['show_on_front'] = $attr['showOnHomepage'];
		}

		if ( isset( $attr['showTrailEnd'] ) ) {
			$args['show_trail_end'] = $attr['showTrailEnd'];
		}

		$home_icon_class_name = '';

		if ( ! empty( $attr['homePrefix'] ) && ! empty( $attr['homePrefixType'] ) ) {
			$home_icon_class_name = "has-home-{$attr['homePrefixType']}-{$attr['homePrefix']}";

			if ( isset( $attr['showHomeLabel'] ) ) {
				$args['show_home_label'] = $attr['showHomeLabel'];
			}
		}

		$sep_class_name =
			! empty( $attr['separator'] ) && ! empty( $attr['separatorType'] )
			? "has-sep-{$attr['separatorType']}-{$attr['separator']}"
			: 'has-sep-image-chevron';

		$justify_class_name =
			empty( $attr['justifyContent'] )
			? ''
			: "is-content-justification-{$attr['justifyContent']}";

		$trail = Trail::render( $args );

		if ( ! $trail ) {
			return '';
		}

		$wrapper_attributes = get_block_wrapper_attributes( [
			'role'       => 'navigation',
			'aria-label' => __( 'Breadcrumbs', 'x3p0-breadcrumbs' ),
			'itemprop'   => 'breadcrumb',
			'class'      => "breadcrumbs {$home_icon_class_name} {$sep_class_name} {$justify_class_name}"
		] );

		return sprintf(
			'<nav %1$s>%2$s</nav>',
			$wrapper_attributes,
			$trail
		);
	}
}
