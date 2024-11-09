<?php
/**
 * The Front Page template file.
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.31
 */

get_header();

// If front-page is a static page
if ( get_option( 'show_on_front' ) == 'page' ) {

	// If Front Page Builder is enabled - display sections
	if ( dazzle_is_on( dazzle_get_theme_option( 'front_page_enabled', false ) ) ) {

		if ( have_posts() ) {
			the_post();
		}

		$dazzle_sections = dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'front_page_sections' ) );
		if ( is_array( $dazzle_sections ) ) {
			foreach ( $dazzle_sections as $dazzle_section ) {
				get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'front-page/section', $dazzle_section ), $dazzle_section );
			}
		}

		// Else if this page is a blog archive
	} elseif ( is_page_template( 'blog.php' ) ) {
		get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'blog' ) );

		// Else - display a native page content
	} else {
		get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'page' ) );
	}

	// Else get the template 'index.php' to show posts
} else {
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'index' ) );
}

get_footer();
