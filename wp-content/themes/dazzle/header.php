<?php
/**
 * The Header: Logo and main menu
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( dazzle_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'dazzle_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'dazzle_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('dazzle_action_body_wrap_attributes'); ?>>

		<?php do_action( 'dazzle_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'dazzle_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('dazzle_action_page_wrap_attributes'); ?>>

			<?php do_action( 'dazzle_action_page_wrap_start' ); ?>

			<?php
			$dazzle_full_post_loading = ( dazzle_is_singular( 'post' ) || dazzle_is_singular( 'attachment' ) ) && dazzle_get_value_gp( 'action' ) == 'full_post_loading';
			$dazzle_prev_post_loading = ( dazzle_is_singular( 'post' ) || dazzle_is_singular( 'attachment' ) ) && dazzle_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $dazzle_full_post_loading && ! $dazzle_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="dazzle_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'dazzle_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to content", 'dazzle' ); ?></a>
				<?php if ( dazzle_sidebar_present() ) { ?>
				<a class="dazzle_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'dazzle_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'dazzle' ); ?></a>
				<?php } ?>
				<a class="dazzle_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'dazzle_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to footer", 'dazzle' ); ?></a>

				<?php
				do_action( 'dazzle_action_before_header' );

				// Header
				$dazzle_header_type = dazzle_get_theme_option( 'header_type' );
				if ( 'custom' == $dazzle_header_type && ! dazzle_is_layouts_available() ) {
					$dazzle_header_type = 'default';
				}
				get_template_part( apply_filters( 'dazzle_filter_get_template_part', "templates/header-" . sanitize_file_name( $dazzle_header_type ) ) );

				// Side menu
				if ( in_array( dazzle_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'dazzle_action_after_header' );

			}
			?>

			<?php do_action( 'dazzle_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( dazzle_is_off( dazzle_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $dazzle_header_type ) ) {
						$dazzle_header_type = dazzle_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $dazzle_header_type && dazzle_is_layouts_available() ) {
						$dazzle_header_id = dazzle_get_custom_header_id();
						if ( $dazzle_header_id > 0 ) {
							$dazzle_header_meta = dazzle_get_custom_layout_meta( $dazzle_header_id );
							if ( ! empty( $dazzle_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$dazzle_footer_type = dazzle_get_theme_option( 'footer_type' );
					if ( 'custom' == $dazzle_footer_type && dazzle_is_layouts_available() ) {
						$dazzle_footer_id = dazzle_get_custom_footer_id();
						if ( $dazzle_footer_id ) {
							$dazzle_footer_meta = dazzle_get_custom_layout_meta( $dazzle_footer_id );
							if ( ! empty( $dazzle_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'dazzle_action_page_content_wrap_class', $dazzle_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'dazzle_filter_is_prev_post_loading', $dazzle_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( dazzle_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'dazzle_action_page_content_wrap_data', $dazzle_prev_post_loading );
			?>>
				<?php
				do_action( 'dazzle_action_page_content_wrap', $dazzle_full_post_loading || $dazzle_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'dazzle_filter_single_post_header', dazzle_is_singular( 'post' ) || dazzle_is_singular( 'attachment' ) ) ) {
					if ( $dazzle_prev_post_loading ) {
						if ( dazzle_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'dazzle_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$dazzle_path = apply_filters( 'dazzle_filter_get_template_part', 'templates/single-styles/' . dazzle_get_theme_option( 'single_style' ) );
					if ( dazzle_get_file_dir( $dazzle_path . '.php' ) != '' ) {
						get_template_part( $dazzle_path );
					}
				}

				// Widgets area above page
				$dazzle_body_style   = dazzle_get_theme_option( 'body_style' );
				$dazzle_widgets_name = dazzle_get_theme_option( 'widgets_above_page' );
				$dazzle_show_widgets = ! dazzle_is_off( $dazzle_widgets_name ) && is_active_sidebar( $dazzle_widgets_name );
				if ( $dazzle_show_widgets ) {
					if ( 'fullscreen' != $dazzle_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					dazzle_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $dazzle_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'dazzle_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $dazzle_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'dazzle_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'dazzle_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="dazzle_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( dazzle_is_singular( 'post' ) || dazzle_is_singular( 'attachment' ) )
							&& $dazzle_prev_post_loading 
							&& dazzle_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'dazzle_action_between_posts' );
						}

						// Widgets area above content
						dazzle_create_widgets_area( 'widgets_above_content' );

						do_action( 'dazzle_action_page_content_start_text' );
