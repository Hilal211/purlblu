<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

if ( dazzle_sidebar_present() ) {
	
	$dazzle_sidebar_type = dazzle_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $dazzle_sidebar_type && ! dazzle_is_layouts_available() ) {
		$dazzle_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $dazzle_sidebar_type ) {
		// Default sidebar with widgets
		$dazzle_sidebar_name = dazzle_get_theme_option( 'sidebar_widgets' );
		dazzle_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $dazzle_sidebar_name ) ) {
			dynamic_sidebar( $dazzle_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$dazzle_sidebar_id = dazzle_get_custom_sidebar_id();
		do_action( 'dazzle_action_show_layout', $dazzle_sidebar_id );
	}
	$dazzle_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $dazzle_out ) ) {
		$dazzle_sidebar_position    = dazzle_get_theme_option( 'sidebar_position' );
		$dazzle_sidebar_position_ss = dazzle_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $dazzle_sidebar_position );
			echo ' sidebar_' . esc_attr( $dazzle_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $dazzle_sidebar_type );

			$dazzle_sidebar_scheme = apply_filters( 'dazzle_filter_sidebar_scheme', dazzle_get_theme_option( 'sidebar_scheme' ) );
			if ( ! empty( $dazzle_sidebar_scheme ) && ! dazzle_is_inherit( $dazzle_sidebar_scheme ) && 'custom' != $dazzle_sidebar_type ) {
				echo ' scheme_' . esc_attr( $dazzle_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="dazzle_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'dazzle_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $dazzle_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$dazzle_title = apply_filters( 'dazzle_filter_sidebar_control_title', 'float' == $dazzle_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'dazzle' ) : '' );
				$dazzle_text  = apply_filters( 'dazzle_filter_sidebar_control_text', 'above' == $dazzle_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'dazzle' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $dazzle_title ); ?>"><?php echo esc_html( $dazzle_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'dazzle_action_before_sidebar', 'sidebar' );
				dazzle_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $dazzle_out ) );
				do_action( 'dazzle_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'dazzle_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}
