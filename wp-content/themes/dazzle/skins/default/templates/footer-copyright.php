<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$dazzle_copyright_scheme = dazzle_get_theme_option( 'copyright_scheme' );
if ( ! empty( $dazzle_copyright_scheme ) && ! dazzle_is_inherit( $dazzle_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $dazzle_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$dazzle_copyright = dazzle_get_theme_option( 'copyright' );
			if ( ! empty( $dazzle_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$dazzle_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $dazzle_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$dazzle_copyright = dazzle_prepare_macros( $dazzle_copyright );
				// Display copyright
				echo wp_kses( nl2br( $dazzle_copyright ), 'dazzle_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
