<?php
/**
 * The template to display Admin notices
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.64
 */

$dazzle_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$dazzle_skins_args = get_query_var( 'dazzle_skins_notice_args' );
?>
<div class="dazzle_admin_notice dazzle_skins_notice notice notice-info is-dismissible" data-notice="skins">
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
		<?php esc_html_e( 'New skins are available', 'dazzle' ); ?>
	</h3>
	<?php

	// Description
	$dazzle_total      = $dazzle_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$dazzle_skins_msg  = $dazzle_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $dazzle_total, 'dazzle' ), $dazzle_total ) . '</strong>'
							: '';
	$dazzle_total      = $dazzle_skins_args['free'];
	$dazzle_skins_msg .= $dazzle_total > 0
							? ( ! empty( $dazzle_skins_msg ) ? ' ' . esc_html__( 'and', 'dazzle' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $dazzle_total, 'dazzle' ), $dazzle_total ) . '</strong>'
							: '';
	$dazzle_total      = $dazzle_skins_args['pay'];
	$dazzle_skins_msg .= $dazzle_skins_args['pay'] > 0
							? ( ! empty( $dazzle_skins_msg ) ? ' ' . esc_html__( 'and', 'dazzle' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $dazzle_total, 'dazzle' ), $dazzle_total ) . '</strong>'
							: '';
	?>
	<div class="dazzle_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'dazzle' ), $dazzle_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="dazzle_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $dazzle_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'dazzle' );
			?>
		</a>
	</div>
</div>
