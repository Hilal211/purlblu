<?php
/**
 * Skin Demo importer
 *
 * @package DAZZLE
 * @since DAZZLE 1.76.0
 */


// Theme storage
//-------------------------------------------------------------------------

dazzle_storage_set( 'theme_demo_url', '//dazzle.themerex.net' );

// Demofiles for RTL
function trl_theme_data($val, $param) {
	if($param== 'theme_demofiles_archive_name')
		$val = 'demo/default-rtl.zip';
	return $val;
}

//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'dazzle_skin_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'dazzle_skin_importer_set_options', 9 );
	function dazzle_skin_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			$demo_type = function_exists( 'dazzle_skins_get_current_skin_name' ) ? dazzle_skins_get_current_skin_name() : 'default';
			if ($demo_type == 'default' && is_rtl()) {
				$demo_type = 'default-rtl';
				dazzle_storage_set( 'theme_demo_url', '//rtl.dazzle.themerex.net' );
				add_filter( 'trx_addons_filter_get_theme_data', 'trl_theme_data', 10, 2 );
			}
			if ( 'default' != $demo_type ) {
				$options['demo_type'] = $demo_type;
				$options['files'][ $demo_type ] = $options['files']['default'];
				unset($options['files']['default']);
			}
			$options['files'][ $demo_type ]['title']       = esc_html__( 'Dazzle Demo', 'dazzle' );
			$options['files'][ $demo_type ]['domain_dev']  = '';  // Developers domain
			$options['files'][ $demo_type ]['domain_demo'] = dazzle_storage_get( 'theme_demo_url' ); // Demo-site domain

			if ( substr( $options['files'][ $demo_type ]['domain_demo'], 0, 2 ) === '//' ) {
				$options['files'][ $demo_type ]['domain_demo'] = dazzle_get_protocol() . ':' . $options['files'][ $demo_type ]['domain_demo'];
			}
		}
		return $options;
	}
}


//------------------------------------------------------------------------
// OCDI support
//------------------------------------------------------------------------

// Set theme specific OCDI options
if ( ! function_exists( 'dazzle_skin_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'dazzle_skin_ocdi_set_options', 9 );
	function dazzle_skin_ocdi_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Demo-site domain
			$options['files']['ocdi']['title']       = esc_html__( 'Dazzle OCDI Demo', 'dazzle' );
			$options['files']['ocdi']['domain_demo'] = dazzle_storage_get( 'theme_demo_url' );
			if ( substr( $options['files']['ocdi']['domain_demo'], 0, 2 ) === '//' ) {
				$options['files']['ocdi']['domain_demo'] = dazzle_get_protocol() . ':' . $options['files']['ocdi']['domain_demo'];
			}
			// If theme need more demo - just copy 'default' and change required parameters
		}
		return $options;
	}
}
