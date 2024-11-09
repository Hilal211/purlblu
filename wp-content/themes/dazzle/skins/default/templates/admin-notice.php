<?php
/**
 * The template to display Admin notices
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.1
 */

$dazzle_theme_slug = get_option( 'template' );
$dazzle_theme_obj  = wp_get_theme( $dazzle_theme_slug );
?>
<div class="dazzle_admin_notice dazzle_welcome_notice notice notice-info is-dismissible" data-notice="admin">
	<?php
	// Theme image
	$dazzle_theme_img = dazzle_get_file_url( 'screenshot.jpg' );
	if ( '' != $dazzle_theme_img ) {
		?>
		<div class="dazzle_notice_image"><img src="<?php echo esc_url( $dazzle_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'dazzle' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="dazzle_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'dazzle' ),
				$dazzle_theme_obj->get( 'Name' ) . ( DAZZLE_THEME_FREE ? ' ' . __( 'Free', 'dazzle' ) : '' ),
				$dazzle_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="dazzle_notice_text">
		<p class="dazzle_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $dazzle_theme_obj->description ) );
			?>
		</p>
		<p class="dazzle_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'dazzle' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="dazzle_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=dazzle_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'dazzle' );
			?>
		</a>
	</div>
</div>
