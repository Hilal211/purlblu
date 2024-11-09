<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

$dazzle_template = apply_filters( 'dazzle_filter_get_template_part', dazzle_blog_archive_get_template() );

if ( ! empty( $dazzle_template ) && 'index' != $dazzle_template ) {

	get_template_part( $dazzle_template );

} else {

	dazzle_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$dazzle_stickies   = is_home()
								|| ( in_array( dazzle_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) dazzle_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$dazzle_post_type  = dazzle_get_theme_option( 'post_type' );
		$dazzle_args       = array(
								'blog_style'     => dazzle_get_theme_option( 'blog_style' ),
								'post_type'      => $dazzle_post_type,
								'taxonomy'       => dazzle_get_post_type_taxonomy( $dazzle_post_type ),
								'parent_cat'     => dazzle_get_theme_option( 'parent_cat' ),
								'posts_per_page' => dazzle_get_theme_option( 'posts_per_page' ),
								'sticky'         => dazzle_get_theme_option( 'sticky_style' ) == 'columns'
															&& is_array( $dazzle_stickies )
															&& count( $dazzle_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		dazzle_blog_archive_start();

		do_action( 'dazzle_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'dazzle_action_before_page_author' );
			get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'dazzle_action_after_page_author' );
		}

		if ( dazzle_get_theme_option( 'show_filters' ) ) {
			do_action( 'dazzle_action_before_page_filters' );
			dazzle_show_filters( $dazzle_args );
			do_action( 'dazzle_action_after_page_filters' );
		} else {
			do_action( 'dazzle_action_before_page_posts' );
			dazzle_show_posts( array_merge( $dazzle_args, array( 'cat' => $dazzle_args['parent_cat'] ) ) );
			do_action( 'dazzle_action_after_page_posts' );
		}

		do_action( 'dazzle_action_blog_archive_end' );

		dazzle_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
