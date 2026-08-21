const path          = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyPlugin    = require('copy-webpack-plugin');

module.exports = {
	...defaultConfig,
	...{
		// `wp-scripts` builds its entry list by scanning the source folder
		// for `block.json` files, so anything that isn't a block has to be
		// added by hand. The leading `../` in the entry name walks the
		// configured output folder (`public/blocks`) back up to `public`,
		// keeping non-block scripts out of the blocks folder rather than
		// filing an editor plugin under a name it doesn't answer to.
		entry: () => ({
			...defaultConfig.entry(),
			'../editor/index': path.resolve(
				__dirname,
				'resources/editor/index.js'
			)
		}),

		plugins: [
			// Include WP's plugin config.
			...defaultConfig.plugins,

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
