<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.06
 */

$dazzle_header_css   = '';
$dazzle_header_image = get_header_image();
$dazzle_header_video = dazzle_get_header_video();
if ( ! empty( $dazzle_header_image ) && dazzle_trx_addons_featured_image_override( is_singular() || dazzle_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$dazzle_header_image = dazzle_get_current_mode_image( $dazzle_header_image );
}

$dazzle_header_id = dazzle_get_custom_header_id();
$dazzle_header_meta = get_post_meta( $dazzle_header_id, 'trx_addons_options', true );
if ( ! empty( $dazzle_header_meta['margin'] ) ) {
	dazzle_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( dazzle_prepare_css_value( $dazzle_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $dazzle_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $dazzle_header_id ) ) ); ?>
				<?php
				echo ! empty( $dazzle_header_image ) || ! empty( $dazzle_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
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

	// Custom header's layout
	do_action( 'dazzle_action_show_layout', $dazzle_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
