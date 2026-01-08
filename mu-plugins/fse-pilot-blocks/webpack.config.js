/**
 * Webpack configuration for FSE Pilot Blocks.
 *
 * Extends the default @wordpress/scripts config to include
 * non-block JavaScript entries like the query-offset extension.
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const CopyPlugin = require( 'copy-webpack-plugin' );
const path = require( 'path' );

const srcDir = path.resolve( __dirname, 'src' );
const buildDir = path.resolve( __dirname, 'build' );

// Filter out the default CopyPlugin and add our own with correct paths
const plugins = defaultConfig.plugins.filter(
	( plugin ) => plugin.constructor.name !== 'CopyPlugin'
);

plugins.push(
	new CopyPlugin( {
		patterns: [
			{
				from: '**/block.json',
				context: srcDir,
				noErrorOnMissing: true,
			},
			{
				from: '**/*.php',
				context: srcDir,
				noErrorOnMissing: true,
			},
		],
	} )
);

module.exports = {
	...defaultConfig,
	plugins,
	entry: {
		// Custom blocks
		'dummy-subscribe-form/index': path.resolve(
			srcDir,
			'dummy-subscribe-form/index.js'
		),
		'featured-posts-grid/index': path.resolve(
			srcDir,
			'featured-posts-grid/index.js'
		),
		'posts-grid/index': path.resolve( srcDir, 'posts-grid/index.js' ),
		'table-of-contents/index': path.resolve(
			srcDir,
			'table-of-contents/index.js'
		),
		'table-of-contents/view': path.resolve(
			srcDir,
			'table-of-contents/view.js'
		),
		// Query offset extension (not a block, just JS filter)
		'query-offset/index': path.resolve( srcDir, 'query-offset/index.js' ),
	},
	output: {
		...defaultConfig.output,
		path: buildDir,
	},
};
