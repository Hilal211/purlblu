<?php
/**
 * The template to display the widgets area in the header
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

// Header sidebar
$dazzle_header_name    = dazzle_get_theme_option( 'header_widgets' );
$dazzle_header_present = ! dazzle_is_off( $dazzle_header_name ) && is_active_sidebar( $dazzle_header_name );
if ( $dazzle_header_present ) {
	dazzle_storage_set( 'current_sidebar', 'header' );
	$dazzle_header_wide = dazzle_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $dazzle_header_name ) ) {
		dynamic_sidebar( $dazzle_header_name );
	}
	$dazzle_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $dazzle_widgets_output ) ) {
		$dazzle_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $dazzle_widgets_output );
		$dazzle_need_columns   = strpos( $dazzle_widgets_output, 'columns_wrap' ) === false;
		if ( $dazzle_need_columns ) {
			$dazzle_columns = max( 0, (int) dazzle_get_theme_option( 'header_columns' ) );
			if ( 0 == $dazzle_columns ) {
				$dazzle_columns = min( 6, max( 1, dazzle_tags_count( $dazzle_widgets_output, 'aside' ) ) );
			}
			if ( $dazzle_columns > 1 ) {
				$dazzle_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $dazzle_columns ) . ' widget', $dazzle_widgets_output );
			} else {
				$dazzle_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $dazzle_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'dazzle_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $dazzle_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $dazzle_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'dazzle_action_before_sidebar', 'header' );
				dazzle_show_layout( $dazzle_widgets_output );
				do_action( 'dazzle_action_after_sidebar', 'header' );
				if ( $dazzle_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $dazzle_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'dazzle_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
