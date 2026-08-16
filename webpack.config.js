const path          = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyPlugin    = require('copy-webpack-plugin');

module.exports = {
	...defaultConfig,
	...{
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
