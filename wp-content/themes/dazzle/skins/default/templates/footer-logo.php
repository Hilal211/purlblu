<?php
/**
 * The template to display the site logo in the footer
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.10
 */

// Logo
if ( dazzle_is_on( dazzle_get_theme_option( 'logo_in_footer' ) ) ) {
	$dazzle_logo_image = dazzle_get_logo_image( 'footer' );
	$dazzle_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $dazzle_logo_image['logo'] ) || ! empty( $dazzle_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $dazzle_logo_image['logo'] ) ) {
					$dazzle_attr = dazzle_getimagesize( $dazzle_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $dazzle_logo_image['logo'] ) . '"'
								. ( ! empty( $dazzle_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $dazzle_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'dazzle' ) . '"'
								. ( ! empty( $dazzle_attr[3] ) ? ' ' . wp_kses_data( $dazzle_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $dazzle_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $dazzle_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
