<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'dazzle_extra_get_css' ) ) {
	add_filter( 'dazzle_filter_get_css', 'dazzle_extra_get_css', 10, 2 );
	function dazzle_extra_get_css( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

			
			.trx_addons_bg_text_char {
				{$fonts['h5_font-family']}
			}



CSS;
		}

		return $css;
	}
}