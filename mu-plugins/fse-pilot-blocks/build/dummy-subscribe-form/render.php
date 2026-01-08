<?php
/**
 * Renders the dummy subscribe form block on the frontend.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string The rendered block HTML.
 */

$fse_pilot_blocks_wrapper_attributes = get_block_wrapper_attributes();
?>
<form <?php echo $fse_pilot_blocks_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<input type="text" class="form-input" placeholder="<?php esc_attr_e( 'Your email address', 'fse-pilot-blocks' ); ?>" />
	<button type="submit" class="btn-secondary wp-block-button__link"><?php esc_html_e( 'Sign Up', 'fse-pilot-blocks' ); ?></button>
</form>
