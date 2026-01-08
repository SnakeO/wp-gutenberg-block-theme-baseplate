<?php
/**
 * Table of Contents block server-side rendering.
 *
 * @package fse-pilot-blocks
 */

// Get post ID from block context or fallback to current post.
$fse_pilot_blocks_post_id = $block->context['postId'] ?? get_the_ID();

if ( ! $fse_pilot_blocks_post_id ) {
	return;
}

$fse_pilot_blocks_post = get_post( $fse_pilot_blocks_post_id );

if ( null === $fse_pilot_blocks_post || '' === $fse_pilot_blocks_post->post_content ) {
	return;
}

// Parse blocks to extract headings.
$fse_pilot_blocks_parsed   = parse_blocks( $fse_pilot_blocks_post->post_content );
$fse_pilot_blocks_headings = fse_pilot_blocks_extract_headings( $fse_pilot_blocks_parsed );

// Don't render if no headings found.
if ( count( $fse_pilot_blocks_headings ) === 0 ) {
	return;
}

/**
 * Recursively extract H2 and H3 headings from blocks.
 *
 * @param array<int|string, array<string, mixed>> $blocks Array of parsed blocks.
 *
 * @return array<int, array<string, mixed>> Array of heading data.
 */
function fse_pilot_blocks_extract_headings( array $blocks ): array {
	$headings = array();

	foreach ( $blocks as $block ) {
		// Check if this is a heading block.
		if ( 'core/heading' === $block['blockName'] ) {
			$level = $block['attrs']['level'] ?? 2;

			// Only include H2 and H3.
			if ( in_array( $level, array( 2, 3 ), true ) ) {
				// Extract text content from innerHTML.
				$text = wp_strip_all_tags( $block['innerHTML'] );
				$text = trim( $text );

				if ( '' !== $text ) {
					$headings[] = array(
						'level' => $level,
						'text'  => $text,
						'id'    => fse_pilot_blocks_generate_heading_id( $text ),
					);
				}
			}
		}

		// Recursively check inner blocks.
		if ( isset( $block['innerBlocks'] ) && count( $block['innerBlocks'] ) > 0 ) {
			$headings = array_merge( $headings, fse_pilot_blocks_extract_headings( $block['innerBlocks'] ) );
		}
	}

	return $headings;
}

/**
 * Generate a sanitized ID from heading text.
 *
 * @param string $text Heading text.
 *
 * @return string Sanitized ID.
 */
function fse_pilot_blocks_generate_heading_id( string $text ): string {
	// Convert to lowercase, replace spaces with hyphens, remove special chars.
	$slug = sanitize_title( $text );
	return 'toc-' . $slug;
}

/**
 * Build hierarchical TOC structure (H3s nested under H2s).
 *
 * @param array<int, array<string, mixed>> $headings Flat array of headings.
 *
 * @return array<int, array<string, mixed>> Hierarchical array with H3s nested under H2s.
 */
function fse_pilot_blocks_build_toc_hierarchy( array $headings ): array {
	$hierarchy  = array();
	$current_h2 = null;

	foreach ( $headings as $heading ) {
		if ( 2 === $heading['level'] ) {
			// Start a new H2 section.
			$hierarchy[] = array(
				'heading'  => $heading,
				'children' => array(),
			);
			$current_h2  = count( $hierarchy ) - 1;
		} elseif ( 3 === $heading['level'] ) {
			// Add H3 as child of current H2.
			if ( null !== $current_h2 ) {
				$hierarchy[ $current_h2 ]['children'][] = $heading;
			} else {
				// H3 without parent H2 - add as top-level.
				$hierarchy[] = array(
					'heading'  => $heading,
					'children' => array(),
				);
			}
		}
	}

	return $hierarchy;
}

// Build the hierarchy.
$fse_pilot_blocks_toc_hierarchy = fse_pilot_blocks_build_toc_hierarchy( $fse_pilot_blocks_headings );

// Get block wrapper attributes.
$fse_pilot_blocks_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'wp-block-fse-pilot-table-of-contents',
	)
);
?>

<div <?php echo $fse_pilot_blocks_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="toc-header">
		<p class="toc-title"><?php esc_html_e( 'Table of Contents', 'fse-pilot-blocks' ); ?></p>
	</div>

	<nav class="toc-list" aria-label="<?php esc_attr_e( 'Table of Contents', 'fse-pilot-blocks' ); ?>">
		<?php foreach ( $fse_pilot_blocks_toc_hierarchy as $fse_pilot_blocks_item ) : ?>
			<a href="#<?php echo esc_attr( $fse_pilot_blocks_item['heading']['id'] ); ?>" class="toc-item toc-item--h<?php echo esc_attr( $fse_pilot_blocks_item['heading']['level'] ); ?>">
				<?php echo esc_html( $fse_pilot_blocks_item['heading']['text'] ); ?>
			</a>

			<?php if ( isset( $fse_pilot_blocks_item['children'] ) && count( $fse_pilot_blocks_item['children'] ) > 0 ) : ?>
				<div class="toc-sublist">
					<?php foreach ( $fse_pilot_blocks_item['children'] as $fse_pilot_blocks_child ) : ?>
						<a href="#<?php echo esc_attr( $fse_pilot_blocks_child['id'] ); ?>" class="toc-item toc-item--h3">
							<?php echo esc_html( $fse_pilot_blocks_child['text'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
</div>
