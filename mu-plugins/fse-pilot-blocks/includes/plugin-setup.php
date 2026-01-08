<?php

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets, so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @since   0.1.0
 * @version 0.1.0
 *
 * @return  void
 */
function fse_pilot_blocks_init(): void {
	register_block_type( FSE_PILOT_BLOCKS_DIR_PATH . 'build/dummy-subscribe-form' );
	register_block_type( FSE_PILOT_BLOCKS_DIR_PATH . 'build/table-of-contents' );
}
add_action( 'init', 'fse_pilot_blocks_init' );

/**
 * Loads the features plugin's translated strings.
 *
 * @version 0.1.0
 *
 * @return  void
 */
function fse_pilot_blocks_load_textdomain(): void {
	load_muplugin_textdomain(
		fse_pilot_blocks_get_metadata( 'TextDomain' ),
		dirname( plugin_basename( constant( 'FSE_PILOT_BLOCKS_DIR_PATH' ) ) ) . fse_pilot_blocks_get_metadata( 'DomainPath' )
	);
}
add_action( 'init', 'fse_pilot_blocks_load_textdomain' );
