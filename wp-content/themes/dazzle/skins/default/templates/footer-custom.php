<?php
/**
 * The template to display default site footer
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.10
 */

$dazzle_footer_id = dazzle_get_custom_footer_id();
$dazzle_footer_meta = get_post_meta( $dazzle_footer_id, 'trx_addons_options', true );
if ( ! empty( $dazzle_footer_meta['margin'] ) ) {
	dazzle_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( dazzle_prepare_css_value( $dazzle_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $dazzle_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $dazzle_footer_id ) ) ); ?>
						<?php
						$dazzle_footer_scheme = dazzle_get_theme_option( 'footer_scheme' );
						if ( ! empty( $dazzle_footer_scheme ) && ! dazzle_is_inherit( $dazzle_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $dazzle_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'dazzle_action_show_layout', $dazzle_footer_id );
	?>
</footer><!-- /.footer_wrap -->
