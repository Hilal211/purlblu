<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'dazzle_essential_grid_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'dazzle_essential_grid_theme_setup9', 9 );
	function dazzle_essential_grid_theme_setup9() {
		if ( dazzle_exists_essential_grid() ) {
			add_action( 'wp_enqueue_scripts', 'dazzle_essential_grid_frontend_scripts', 1100 );
			add_action( 'trx_addons_action_load_scripts_front_essential_grid', 'dazzle_essential_grid_frontend_scripts', 10, 1 );
			add_filter( 'dazzle_filter_merge_styles', 'dazzle_essential_grid_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'dazzle_filter_tgmpa_required_plugins', 'dazzle_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'dazzle_essential_grid_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('dazzle_filter_tgmpa_required_plugins',	'dazzle_essential_grid_tgmpa_required_plugins');
	function dazzle_essential_grid_tgmpa_required_plugins( $list = array() ) {
		if ( dazzle_storage_isset( 'required_plugins', 'essential-grid' ) && dazzle_storage_get_array( 'required_plugins', 'essential-grid', 'install' ) !== false && dazzle_is_theme_activated() ) {
			$path = dazzle_get_plugin_source_path( 'plugins/essential-grid/essential-grid.zip' );
			if ( ! empty( $path ) || dazzle_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => dazzle_storage_get_array( 'required_plugins', 'essential-grid', 'title' ),
					'slug'     => 'essential-grid',
					'source'   => ! empty( $path ) ? $path : 'upload://essential-grid.zip',
					'version'  => '2.2.4.2',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'dazzle_exists_essential_grid' ) ) {
	function dazzle_exists_essential_grid() {
		return defined( 'EG_PLUGIN_PATH' ) || defined( 'ESG_PLUGIN_PATH' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'dazzle_essential_grid_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'dazzle_essential_grid_frontend_scripts', 1100 );
	//Handler of the add_action( 'trx_addons_action_load_scripts_front_essential_grid', 'dazzle_essential_grid_frontend_scripts', 10, 1 );
	function dazzle_essential_grid_frontend_scripts( $force = false ) {
		dazzle_enqueue_optimized( 'essential_grid', $force, array(
			'css' => array(
				'dazzle-essential-grid' => array( 'src' => 'plugins/essential-grid/essential-grid.css' ),
			)
		) );
	}
}

// Merge custom styles
if ( ! function_exists( 'dazzle_essential_grid_merge_styles' ) ) {
	//Handler of the add_filter('dazzle_filter_merge_styles', 'dazzle_essential_grid_merge_styles');
	function dazzle_essential_grid_merge_styles( $list ) {
		$list[ 'plugins/essential-grid/essential-grid.css' ] = false;
		return $list;
	}
}
