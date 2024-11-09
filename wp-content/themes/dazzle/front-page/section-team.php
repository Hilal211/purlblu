<div class="front_page_section front_page_section_team<?php
	$dazzle_scheme = dazzle_get_theme_option( 'front_page_team_scheme' );
	if ( ! empty( $dazzle_scheme ) && ! dazzle_is_inherit( $dazzle_scheme ) ) {
		echo ' scheme_' . esc_attr( $dazzle_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( dazzle_get_theme_option( 'front_page_team_paddings' ) );
	if ( dazzle_get_theme_option( 'front_page_team_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$dazzle_css      = '';
		$dazzle_bg_image = dazzle_get_theme_option( 'front_page_team_bg_image' );
		if ( ! empty( $dazzle_bg_image ) ) {
			$dazzle_css .= 'background-image: url(' . esc_url( dazzle_get_attachment_url( $dazzle_bg_image ) ) . ');';
		}
		if ( ! empty( $dazzle_css ) ) {
			echo ' style="' . esc_attr( $dazzle_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$dazzle_anchor_icon = dazzle_get_theme_option( 'front_page_team_anchor_icon' );
	$dazzle_anchor_text = dazzle_get_theme_option( 'front_page_team_anchor_text' );
if ( ( ! empty( $dazzle_anchor_icon ) || ! empty( $dazzle_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_team"'
									. ( ! empty( $dazzle_anchor_icon ) ? ' icon="' . esc_attr( $dazzle_anchor_icon ) . '"' : '' )
									. ( ! empty( $dazzle_anchor_text ) ? ' title="' . esc_attr( $dazzle_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_team_inner
	<?php
	if ( dazzle_get_theme_option( 'front_page_team_fullheight' ) ) {
		echo ' dazzle-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$dazzle_css      = '';
			$dazzle_bg_mask  = dazzle_get_theme_option( 'front_page_team_bg_mask' );
			$dazzle_bg_color_type = dazzle_get_theme_option( 'front_page_team_bg_color_type' );
			if ( 'custom' == $dazzle_bg_color_type ) {
				$dazzle_bg_color = dazzle_get_theme_option( 'front_page_team_bg_color' );
			} elseif ( 'scheme_bg_color' == $dazzle_bg_color_type ) {
				$dazzle_bg_color = dazzle_get_scheme_color( 'bg_color', $dazzle_scheme );
			} else {
				$dazzle_bg_color = '';
			}
			if ( ! empty( $dazzle_bg_color ) && $dazzle_bg_mask > 0 ) {
				$dazzle_css .= 'background-color: ' . esc_attr(
					1 == $dazzle_bg_mask ? $dazzle_bg_color : dazzle_hex2rgba( $dazzle_bg_color, $dazzle_bg_mask )
				) . ';';
			}
			if ( ! empty( $dazzle_css ) ) {
				echo ' style="' . esc_attr( $dazzle_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_team_content_wrap content_wrap">
			<?php
			// Caption
			$dazzle_caption = dazzle_get_theme_option( 'front_page_team_caption' );
			if ( ! empty( $dazzle_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_team_caption front_page_block_<?php echo ! empty( $dazzle_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $dazzle_caption, 'dazzle_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$dazzle_description = dazzle_get_theme_option( 'front_page_team_description' );
			if ( ! empty( $dazzle_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_team_description front_page_block_<?php echo ! empty( $dazzle_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $dazzle_description ), 'dazzle_kses_content' ); ?></div>
				<?php
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_team_output">
				<?php
				if ( is_active_sidebar( 'front_page_team_widgets' ) ) {
					dynamic_sidebar( 'front_page_team_widgets' );
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					if ( ! dazzle_exists_trx_addons() ) {
						dazzle_customizer_need_trx_addons_message();
					} else {
						dazzle_customizer_need_widgets_message( 'front_page_team_caption', 'ThemeREX Addons - Team' );
					}
				}
				?>
			</div>
		</div>
	</div>
</div>
