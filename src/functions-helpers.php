<?php
/**
 * Helper functions.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2021, Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs;

use Hybrid\Mix\Mix;

/**
 * Mini container.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $abstract
 * @return mixed
 */
function app( $abstract = '' ) {
	static $bindings = null;

	if ( is_null( $bindings ) ) {
		$path = untrailingslashit( __DIR__ . '/..' );
		$uri  = untrailingslashit( plugins_url( '/..', __FILE__ ) );

		$bindings = [
			Assets::class => new Assets(
				new Mix( "{$path}/public", "{$uri}/public" )
			),
			Block::class  => new Block( "{$path}/public" )
		];

		foreach ( $bindings as $binding ) {
			$binding->boot();
		}
	}

	return $abstract ? $bindings[ $abstract ] : $bindings;
}
