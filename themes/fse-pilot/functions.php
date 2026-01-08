<?php
/**
 * FSE Pilot functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package FSE Pilot
 * @since FSE Pilot 1.0
 */

/**
 * Sets up theme support and loads text domain.
 *
 * @return void
 */
function fse_pilot_theme_support() {
	add_editor_style( 'style-editor.css' );
	load_theme_textdomain( 'fse-pilot' );
}
add_action( 'after_setup_theme', 'fse_pilot_theme_support' );

/**
 * Enqueues theme styles.
 *
 * @return void
 */
function fse_pilot_theme_styles() {
	wp_register_style(
		'fse-pilot-style',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);
	wp_enqueue_style( 'fse-pilot-style' );
}
add_action( 'wp_enqueue_scripts', 'fse_pilot_theme_styles' );

/**
 * Enqueues theme scripts.
 *
 * @return void
 */
function fse_pilot_theme_scripts() {
	wp_enqueue_script(
		'fse-pilot-scripts',
		get_template_directory_uri() . '/assets/js/build/index.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'fse_pilot_theme_scripts' );

/**
 * Registers custom block styles.
 *
 * @return void
 */
function fse_pilot_register_block_styles() {
	$block_styles = array(
		'core/button'  => array(
			'secondary-button' => __( 'Secondary', 'fse-pilot' ),
		),
		'core/heading' => array(
			'with-separator' => __( 'With Separator', 'fse-pilot' ),
		),
	);

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style_name => $style_label ) {
			register_block_style(
				$block,
				array(
					'name'  => $style_name,
					'label' => $style_label,
				)
			);
		}
	}
}
add_action( 'init', 'fse_pilot_register_block_styles' );

/**
 * Enqueues custom block stylesheets from the assets/css directory.
 *
 * @return void
 */
function fse_pilot_enqueue_custom_block_styles() {
	$files = glob( get_template_directory() . '/assets/css/*.css' );
	if ( is_array( $files ) ) {
		foreach ( $files as $file ) {
			$filename   = basename( $file, '.css' );
			$block_name = str_replace( 'core-', 'core/', $filename );

			wp_enqueue_block_style(
				$block_name,
				array(
					'handle' => "fse-pilot-block-{$filename}",
					'src'    => get_theme_file_uri( "assets/css/{$filename}.css" ),
					'path'   => get_theme_file_path( "assets/css/{$filename}.css" ),
				)
			);
		}
	}
}
add_action( 'init', 'fse_pilot_enqueue_custom_block_styles' );

/**
 * Enables comments by default.
 *
 * @return void
 */
function fse_pilot_enable_comments() {
	update_option( 'default_comment_status', 'open' );
}
add_action( 'after_setup_theme', 'fse_pilot_enable_comments' );

/**
 * Adds spacing support to the query pagination block.
 *
 * @param array<string, mixed> $args       Block type arguments.
 * @param string               $block_type Block type name.
 *
 * @return array<string, mixed> Modified block type arguments.
 */
function fse_pilot_add_pagination_spacing_support( $args, $block_type ) {
	if ( 'core/query-pagination' === $block_type ) {
		$args['supports'] = array_merge(
			$args['supports'] ?? array(),
			array(
				'spacing' => array(
					'margin'  => true,
					'padding' => true,
				),
			)
		);
	}
	return $args;
}
add_filter( 'register_block_type_args', 'fse_pilot_add_pagination_spacing_support', 10, 2 );

/**
 * Always shows the previous pagination link, even when disabled.
 *
 * @param string               $block_content Block content.
 * @param array<string, mixed> $block         Block attributes.
 *
 * @return string Modified block content.
 */
function fse_pilot_always_show_pagination_previous( $block_content, $block ) {
	$label = $block['attrs']['label'] ?? __( 'Previous', 'fse-pilot' );

	if ( '' !== trim( $block_content ) ) {
		return str_replace( 'Previous Page', $label, $block_content );
	}

	return sprintf(
		'<span class="wp-block-query-pagination-previous pagination-disabled" aria-disabled="true"><span class="wp-block-query-pagination-previous-arrow is-arrow-arrow" aria-hidden="true"></span>%s</span>',
		esc_html( $label )
	);
}
add_filter( 'render_block_core/query-pagination-previous', 'fse_pilot_always_show_pagination_previous', 10, 2 );

/**
 * Always shows the next pagination link, even when disabled.
 *
 * @param string               $block_content Block content.
 * @param array<string, mixed> $block         Block attributes.
 *
 * @return string Modified block content.
 */
function fse_pilot_always_show_pagination_next( $block_content, $block ) {
	$label = $block['attrs']['label'] ?? __( 'Next', 'fse-pilot' );

	if ( '' !== trim( $block_content ) ) {
		return str_replace( 'Next Page', $label, $block_content );
	}

	return sprintf(
		'<span class="wp-block-query-pagination-next pagination-disabled" aria-disabled="true">%s<span class="wp-block-query-pagination-next-arrow is-arrow-arrow" aria-hidden="true"></span></span>',
		esc_html( $label )
	);
}
add_filter( 'render_block_core/query-pagination-next', 'fse_pilot_always_show_pagination_next', 10, 2 );

/**
 * Cleans up archive titles by removing prefixes.
 *
 * @param string $title Archive title.
 *
 * @return string Cleaned archive title.
 */
function fse_pilot_clean_archive_title( $title ) {
	if ( is_category() ) {
		return single_cat_title( '', false ) ?? $title;
	} elseif ( is_tag() ) {
		return single_tag_title( '', false ) ?? $title;
	} elseif ( is_author() ) {
		return get_the_author() ?? $title;
	} elseif ( is_post_type_archive() ) {
		return post_type_archive_title( '', false ) ?? $title;
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'fse_pilot_clean_archive_title' );

/**
 * Removes inline width and height attributes from featured images.
 *
 * @param string               $block_content Block content.
 * @param array<string, mixed> $block         Block attributes (unused).
 *
 * @return string Modified block content.
 */
function fse_pilot_remove_featured_image_inline_styles( $block_content, $block ) {
	unset( $block );
	$block_content = preg_replace( '/(<img[^>]*)\s*width=("[^"]*"|\d+)/', '$1', $block_content ) ?? $block_content;
	$block_content = preg_replace( '/(<img[^>]*)\s*height=("[^"]*"|\d+)/', '$1', $block_content ) ?? $block_content;
	return $block_content;
}
add_filter( 'render_block_core/post-featured-image', 'fse_pilot_remove_featured_image_inline_styles', 10, 2 );

/**
 * Excludes the current post from query loop blocks on single post pages.
 *
 * @param array<string, mixed> $query Query arguments.
 * @param array<string, mixed> $block Block attributes (unused).
 *
 * @return array<string, mixed> Modified query arguments.
 */
function fse_pilot_exclude_current_post_from_query( $query, $block ) {
	unset( $block );
	if ( is_singular( 'post' ) ) {
		$query['post__not_in'] = array( get_queried_object_id() );
	}
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'fse_pilot_exclude_current_post_from_query', 10, 2 );

/**
 * Changes "Responses" to "Comments" in the comments-title block.
 *
 * @param string               $block_content The block content.
 * @param array<string, mixed> $block         Block attributes (unused).
 *
 * @return string Modified block content.
 */
function fse_pilot_change_responses_to_comments( $block_content, $block ) {
	unset( $block );
	$block_content = str_ireplace( 'responses', 'Comments', $block_content );
	$block_content = str_ireplace( 'response', 'Comment', $block_content );
	return $block_content;
}
add_filter( 'render_block_core/comments-title', 'fse_pilot_change_responses_to_comments', 10, 2 );
