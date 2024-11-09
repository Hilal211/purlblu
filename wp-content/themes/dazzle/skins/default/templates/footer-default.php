<?php
/**
 * The template to display default site footer
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$dazzle_footer_scheme = dazzle_get_theme_option( 'footer_scheme' );
if ( ! empty( $dazzle_footer_scheme ) && ! dazzle_is_inherit( $dazzle_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $dazzle_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/footer-socials' ) );

	// Copyright area
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
