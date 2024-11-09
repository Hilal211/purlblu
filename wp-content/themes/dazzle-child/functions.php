<?php
/**
 * Child-Theme functions and definitions
 */

// Load rtl.css because it is not autoloaded from the child theme
if ( ! function_exists( 'dazzle_child_load_rtl' ) ) {
	add_filter( 'wp_enqueue_scripts', 'dazzle_child_load_rtl', 3000 );
	function dazzle_child_load_rtl() {
		if ( is_rtl() ) {
			wp_enqueue_style( 'dazzle-style-rtl', get_template_directory_uri() . '/rtl.css' );
		}
	}
}

?>