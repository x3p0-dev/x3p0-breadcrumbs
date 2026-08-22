const path                     = require('path');
const defaultConfig            = require('@wordpress/scripts/config/webpack.config');
const CopyPlugin               = require('copy-webpack-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

module.exports = {
	...defaultConfig,
	...{
		// `wp-scripts` builds its entry list by scanning the source folder
		// for `block.json` files, so anything that isn't a block has to be
		// added by hand. The leading `../` in the entry name walks the
		// configured output folder (`public/blocks`) back up to `public`,
		// keeping non-block assets out of the blocks folder rather than
		// filing an editor script under a name it doesn't answer to.
		//
		// A script and its stylesheet are listed separately, and the script
		// does not import the stylesheet. The chunk name is the only thing
		// `MiniCssExtractPlugin` and the RTL plugin will name their output
		// from, so a bundle emitting both from one chunk could only ever
		// file them under one folder. Splitting the entries is what lets
		// each land in its own.
		entry: () => ({
			...defaultConfig.entry(),
			'../js/editor':     path.resolve(__dirname, 'resources/js',   'editor/index.js'),
			'../js/edit-term':  path.resolve(__dirname, 'resources/js',   'edit-term/index.js'),
			'../css/editor':    path.resolve(__dirname, 'resources/scss', 'editor.scss'),
			'../css/edit-term': path.resolve(__dirname, 'resources/scss', 'edit-term.scss')
		}),

		plugins: [
			// Include WP's plugin config.
			...defaultConfig.plugins,

			// Removes the empty `.js` files webpack generates for the
			// stylesheet-only entries above. For this to work correctly,
			// it needs to run after WP has generated the `*.asset.php`
			// files. This is what `STAGE_AFTER_PROCESS_PLUGINS` allows.
			new RemoveEmptyScriptsPlugin({
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS
			}),

			// Copies any assets that don't need to be processed to
			// the output folder.
			new CopyPlugin({
				patterns: [
					{
						from: './resources/media',
						to: path.resolve(__dirname, 'public/media')
					}
				]
			})
		]
	}
};
