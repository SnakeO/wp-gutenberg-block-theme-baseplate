<?php
/**
 * Query Offset extension for core/query block.
 *
 * Enqueues the JavaScript that adds an offset control to the Query Loop
 * block's inspector panel in the block editor.
 *
 * @since   0.1.0
 * @package FSE_Pilot_Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the query offset extension script in the block editor.
 *
 * @since   0.1.0
 * @return  void
 */
function fse_pilot_blocks_enqueue_query_offset(): void {
	$asset_file = FSE_PILOT_BLOCKS_DIR_PATH . 'build/query-offset/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file; // @phpstan-ignore require.fileNotFound

	wp_enqueue_script(
		'fse-pilot-query-offset',
		FSE_PILOT_BLOCKS_DIR_URL . 'build/query-offset/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'fse_pilot_blocks_enqueue_query_offset' );
