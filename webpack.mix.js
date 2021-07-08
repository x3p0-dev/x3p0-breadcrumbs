/**
 * Laravel Mix configuration file.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright 2021 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 */

// Import required packages.
const mix = require( 'laravel-mix' );
const del = require( 'del' );

// Sets the development path for assets to the `/resources` folder.
const devPath    = 'resources';
const publicPath = 'public';

// Sets the path to the generated assets to the `/public` folder.
mix.setPublicPath( publicPath );

// Set Laravel Mix options.
mix.options( {
	postCss        : [ require( 'postcss-preset-env' )() ],
	processCssUrls : false,
	terser: {
		terserOptions: {
			format: { comments: false }
		},
		extractComments: false
	}
} );

// Builds sources maps for assets.
mix.sourceMaps();

// Versioning and cache busting.
mix.version();

// Compile JavaScript.
mix.js( `${devPath}/js/editor.js`, 'js' ).react()
    .then( () => {
	    if ( mix.inProduction() ) {
		    del( `${publicPath}/js/editor.js.map` );
	    }
    } );

// Sass configuration.
var sassConfig = {
	sassOptions: {
		outputStyle : 'expanded',
		indentType  : 'tab',
		indentWidth : 1
	}
};

// Compile SASS/CSS.
mix.sass( `${devPath}/scss/style.scss`,  'css', sassConfig )
   .sass( `${devPath}/scss/editor.scss`, 'css', sassConfig )
   .then( () => {
	   if ( mix.inProduction() ) {
		   del( `${publicPath}/css/style.css.map`  );
		   del( `${publicPath}/css/editor.css.map` );
	   }
   } );

// Copy additional assets.
mix.copy( `${devPath}/block.json`, `${publicPath}/block.json` );

// Add custom Webpack configuration.
mix.webpackConfig( {
	stats       : 'minimal',
	devtool     : mix.inProduction() ? false : 'source-map',
	performance : { hints : false },
	externals   : { 'react' : 'React' }
} );
