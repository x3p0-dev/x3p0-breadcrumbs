<?php
/**
 * Assets class.
 *
 * Sets up and register assets for the plugin.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2021, Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs;

use Hybrid\Mix\Mix;

/**
 * Handles assets.
 *
 * @since  1.0.0
 * @access public
 */
class Assets {

	/**
	 * Instance of the Mix object.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    \Hybrid\Mix\Mix
	 */
	private $mix;

	/**
	 * Sets up object state.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  Mix    $mix
	 * @return void
	 */
	public function __construct( Mix $mix ) {
		$this->mix = $mix;
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
	 * Registers assets with WordPress.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		wp_register_style(
			'x3p0-breadcrumbs',
			$this->mix->asset( 'css/style.css' ), null, null
		);

		wp_register_style(
			'x3p0-breadcrumbs-editor',
			$this->mix->asset( 'css/editor.css' ), null, null
		);

		wp_register_script(
			'x3p0-breadcrumbs-editor',
			$this->mix->asset( 'js/editor.js' ),
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
	}
}
