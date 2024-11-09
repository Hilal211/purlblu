<?php
/**
 * The template to display the background video in the header
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.14
 */
$dazzle_header_video = dazzle_get_header_video();
$dazzle_embed_video  = '';
if ( ! empty( $dazzle_header_video ) && ! dazzle_is_from_uploads( $dazzle_header_video ) ) {
	if ( dazzle_is_youtube_url( $dazzle_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $dazzle_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php dazzle_show_layout( dazzle_get_embed_video( $dazzle_header_video ) ); ?></div>
		<?php
	}
}
