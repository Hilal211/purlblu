<?php
/**
 * The template 'Style 5' to displaying related posts
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.54
 */

$dazzle_link        = get_permalink();
$dazzle_post_format = get_post_format();
$dazzle_post_format = empty( $dazzle_post_format ) ? 'standard' : str_replace( 'post-format-', '', $dazzle_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $dazzle_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	dazzle_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'dazzle_filter_related_thumb_size', dazzle_get_thumb_size( (int) dazzle_get_theme_option( 'related_posts' ) == 1 ? 'big' : 'med' ) ),
		)
	);
	?>
	<div class="post_header entry-header">
		<h6 class="post_title entry-title"><a href="<?php echo esc_url( $dazzle_link ); ?>"><?php
			if ( '' == get_the_title() ) {
				esc_html_e( '- No title -', 'dazzle' );
			} else {
				the_title();
			}
		?></a></h6>
		<?php
		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
			?>
			<div class="post_meta">
				<a href="<?php echo esc_url( $dazzle_link ); ?>" class="post_meta_item post_date"><?php echo wp_kses_data( dazzle_get_date() ); ?></a>
			</div>
			<?php
		}
		?>
	</div>
</div>
