<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

							do_action( 'dazzle_action_page_content_end_text' );
							
							// Widgets area below the content
							dazzle_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'dazzle_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'dazzle_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'dazzle_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'dazzle_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$dazzle_body_style = dazzle_get_theme_option( 'body_style' );
					$dazzle_widgets_name = dazzle_get_theme_option( 'widgets_below_page' );
					$dazzle_show_widgets = ! dazzle_is_off( $dazzle_widgets_name ) && is_active_sidebar( $dazzle_widgets_name );
					$dazzle_show_related = dazzle_is_single() && dazzle_get_theme_option( 'related_position' ) == 'below_page';
					if ( $dazzle_show_widgets || $dazzle_show_related ) {
						if ( 'fullscreen' != $dazzle_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $dazzle_show_related ) {
							do_action( 'dazzle_action_related_posts' );
						}

						// Widgets area below page content
						if ( $dazzle_show_widgets ) {
							dazzle_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $dazzle_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'dazzle_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'dazzle_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! dazzle_is_singular( 'post' ) && ! dazzle_is_singular( 'attachment' ) ) || ! in_array ( dazzle_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="dazzle_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'dazzle_action_before_footer' );

				// Footer
				$dazzle_footer_type = dazzle_get_theme_option( 'footer_type' );
				if ( 'custom' == $dazzle_footer_type && ! dazzle_is_layouts_available() ) {
					$dazzle_footer_type = 'default';
				}
				get_template_part( apply_filters( 'dazzle_filter_get_template_part', "templates/footer-" . sanitize_file_name( $dazzle_footer_type ) ) );

				do_action( 'dazzle_action_after_footer' );

			}
			?>

			<?php do_action( 'dazzle_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'dazzle_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'dazzle_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>