<?php
// Add plugin-specific fonts to the custom CSS
if ( ! function_exists( 'dazzle_elm_get_css' ) ) {
    add_filter( 'dazzle_filter_get_css', 'dazzle_elm_get_css', 10, 2 );
    function dazzle_elm_get_css( $css, $args ) {

        if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
            $fonts         = $args['fonts'];
            $css['fonts'] .= <<<CSS
.elementor-widget-progress .elementor-title,
.elementor-widget-progress .elementor-progress-percentage,
.elementor-widget-toggle .elementor-toggle-title,
.elementor-widget-tabs .elementor-tab-title,
.custom_icon_btn.elementor-widget-button .elementor-button .elementor-button-text,
.elementor-widget-counter .elementor-counter-number-wrapper,
.elementor-widget-counter .elementor-counter-title {
	{$fonts['h5_font-family']}
}
.elementor-widget-icon-box .elementor-widget-container .elementor-icon-box-title small {
    {$fonts['p_font-family']}
}

CSS;
        }

        return $css;
    }
}


// Add theme-specific CSS-animations
if ( ! function_exists( 'dazzle_elm_add_theme_animations' ) ) {
	add_filter( 'elementor/controls/animations/additional_animations', 'dazzle_elm_add_theme_animations' );
	function dazzle_elm_add_theme_animations( $animations ) {
		/* To add a theme-specific animations to the list:
			1) Merge to the array 'animations': array(
													esc_html__( 'Theme Specific', 'dazzle' ) => array(
														'ta_custom_1' => esc_html__( 'Custom 1', 'dazzle' )
													)
												)
			2) Add a CSS rules for the class '.ta_custom_1' to create a custom entrance animation
		*/
		$animations = array_merge(
						$animations,
						array(
							esc_html__( 'Theme Specific', 'dazzle' ) => array(
									'ta_under_strips' => esc_html__( 'Under the strips', 'dazzle' ),
									'dazzle-fadeinup' => esc_html__( 'Dazzle - Fade In Up', 'dazzle' ),
									'dazzle-fadeinright' => esc_html__( 'Dazzle - Fade In Right', 'dazzle' ),
									'dazzle-fadeinleft' => esc_html__( 'Dazzle - Fade In Left', 'dazzle' ),
									'dazzle-fadeindown' => esc_html__( 'Dazzle - Fade In Down', 'dazzle' ),
									'dazzle-fadein' => esc_html__( 'Dazzle - Fade In', 'dazzle' ),
									'dazzle-infinite-rotate' => esc_html__( 'Dazzle - Infinite Rotate', 'dazzle' ),
								)
							)
						);

		return $animations;
	}
}
