<?php
/**
 * The template to display default site header
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

$dazzle_header_css   = '';
$dazzle_header_image = get_header_image();
$dazzle_header_video = dazzle_get_header_video();
if ( ! empty( $dazzle_header_image ) && dazzle_trx_addons_featured_image_override( is_singular() || dazzle_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$dazzle_header_image = dazzle_get_current_mode_image( $dazzle_header_image );
}

?><header class="top_panel top_panel_default
	<?php
	echo ! empty( $dazzle_header_image ) || ! empty( $dazzle_header_video ) ? ' with_bg_image' : ' without_bg_image';
	if ( '' != $dazzle_header_video ) {
		echo ' with_bg_video';
	}
	if ( '' != $dazzle_header_image ) {
		echo ' ' . esc_attr( dazzle_add_inline_css_class( 'background-image: url(' . esc_url( $dazzle_header_image ) . ');' ) );
	}
	if ( is_single() && has_post_thumbnail() ) {
		echo ' with_featured_image';
	}
	if ( dazzle_is_on( dazzle_get_theme_option( 'header_fullheight' ) ) ) {
		echo ' header_fullheight dazzle-full-height';
	}
	$dazzle_header_scheme = dazzle_get_theme_option( 'header_scheme' );
	if ( ! empty( $dazzle_header_scheme ) && ! dazzle_is_inherit( $dazzle_header_scheme  ) ) {
		echo ' scheme_' . esc_attr( $dazzle_header_scheme );
	}
	?>
">
	<?php

	// Background video
	if ( ! empty( $dazzle_header_video ) ) {
		get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-video' ) );
	}

	// Main menu
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-navi' ) );

	// Mobile header
	if ( dazzle_is_on( dazzle_get_theme_option( 'header_mobile_enabled' ) ) ) {
		get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-mobile' ) );
	}

	// Page title and breadcrumbs area
	if ( ! is_single() ) {
		get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-title' ) );
	}

	// Header widgets area
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-widgets' ) );
	?>
</header>
