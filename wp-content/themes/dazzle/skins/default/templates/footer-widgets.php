<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.10
 */

// Footer sidebar
$dazzle_footer_name    = dazzle_get_theme_option( 'footer_widgets' );
$dazzle_footer_present = ! dazzle_is_off( $dazzle_footer_name ) && is_active_sidebar( $dazzle_footer_name );
if ( $dazzle_footer_present ) {
	dazzle_storage_set( 'current_sidebar', 'footer' );
	$dazzle_footer_wide = dazzle_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $dazzle_footer_name ) ) {
		dynamic_sidebar( $dazzle_footer_name );
	}
	$dazzle_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $dazzle_out ) ) {
		$dazzle_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $dazzle_out );
		$dazzle_need_columns = true;   //or check: strpos($dazzle_out, 'columns_wrap')===false;
		if ( $dazzle_need_columns ) {
			$dazzle_columns = max( 0, (int) dazzle_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $dazzle_columns ) {
				$dazzle_columns = min( 4, max( 1, dazzle_tags_count( $dazzle_out, 'aside' ) ) );
			}
			if ( $dazzle_columns > 1 ) {
				$dazzle_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $dazzle_columns ) . ' widget', $dazzle_out );
			} else {
				$dazzle_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $dazzle_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'dazzle_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $dazzle_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $dazzle_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'dazzle_action_before_sidebar', 'footer' );
				dazzle_show_layout( $dazzle_out );
				do_action( 'dazzle_action_after_sidebar', 'footer' );
				if ( $dazzle_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $dazzle_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'dazzle_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
