<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

$dazzle_args = get_query_var( 'dazzle_logo_args' );

// Site logo
$dazzle_logo_type   = isset( $dazzle_args['type'] ) ? $dazzle_args['type'] : '';
$dazzle_logo_image  = dazzle_get_logo_image( $dazzle_logo_type );
$dazzle_logo_text   = dazzle_is_on( dazzle_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$dazzle_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $dazzle_logo_image['logo'] ) || ! empty( $dazzle_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $dazzle_logo_image['logo'] ) ) {
			if ( empty( $dazzle_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric($dazzle_logo_image['logo']) && (int) $dazzle_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$dazzle_attr = dazzle_getimagesize( $dazzle_logo_image['logo'] );
				echo '<img src="' . esc_url( $dazzle_logo_image['logo'] ) . '"'
						. ( ! empty( $dazzle_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $dazzle_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $dazzle_logo_text ) . '"'
						. ( ! empty( $dazzle_attr[3] ) ? ' ' . wp_kses_data( $dazzle_attr[3] ) : '' )
						. '>';
			}
		} else {
			dazzle_show_layout( dazzle_prepare_macros( $dazzle_logo_text ), '<span class="logo_text">', '</span>' );
			dazzle_show_layout( dazzle_prepare_macros( $dazzle_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
