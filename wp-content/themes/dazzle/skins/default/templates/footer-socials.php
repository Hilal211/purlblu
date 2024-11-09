<?php
/**
 * The template to display the socials in the footer
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.10
 */


// Socials
if ( dazzle_is_on( dazzle_get_theme_option( 'socials_in_footer' ) ) ) {
	$dazzle_output = dazzle_get_socials_links();
	if ( '' != $dazzle_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php dazzle_show_layout( $dazzle_output ); ?>
			</div>
		</div>
		<?php
	}
}
