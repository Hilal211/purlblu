<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package DAZZLE
 * @since DAZZLE 1.0.50
 */

$dazzle_template_args = get_query_var( 'dazzle_template_args' );
if ( is_array( $dazzle_template_args ) ) {
	$dazzle_columns    = empty( $dazzle_template_args['columns'] ) ? 2 : max( 1, $dazzle_template_args['columns'] );
	$dazzle_blog_style = array( $dazzle_template_args['type'], $dazzle_columns );
} else {
	$dazzle_template_args = array();
	$dazzle_blog_style = explode( '_', dazzle_get_theme_option( 'blog_style' ) );
	$dazzle_columns    = empty( $dazzle_blog_style[1] ) ? 2 : max( 1, $dazzle_blog_style[1] );
}
$dazzle_blog_id       = dazzle_get_custom_blog_id( join( '_', $dazzle_blog_style ) );
$dazzle_blog_style[0] = str_replace( 'blog-custom-', '', $dazzle_blog_style[0] );
$dazzle_expanded      = ! dazzle_sidebar_present() && dazzle_get_theme_option( 'expand_content' ) == 'expand';
$dazzle_components    = ! empty( $dazzle_template_args['meta_parts'] )
							? ( is_array( $dazzle_template_args['meta_parts'] )
								? join( ',', $dazzle_template_args['meta_parts'] )
								: $dazzle_template_args['meta_parts']
								)
							: dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'meta_parts' ) );
$dazzle_post_format   = get_post_format();
$dazzle_post_format   = empty( $dazzle_post_format ) ? 'standard' : str_replace( 'post-format-', '', $dazzle_post_format );

$dazzle_blog_meta     = dazzle_get_custom_layout_meta( $dazzle_blog_id );
$dazzle_custom_style  = ! empty( $dazzle_blog_meta['scripts_required'] ) ? $dazzle_blog_meta['scripts_required'] : 'none';

if ( ! empty( $dazzle_template_args['slider'] ) || $dazzle_columns > 1 || ! dazzle_is_off( $dazzle_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $dazzle_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( dazzle_is_off( $dazzle_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $dazzle_custom_style ) ) . "-1_{$dazzle_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $dazzle_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $dazzle_columns )
					. ' post_layout_' . esc_attr( $dazzle_blog_style[0] )
					. ' post_layout_' . esc_attr( $dazzle_blog_style[0] ) . '_' . esc_attr( $dazzle_columns )
					. ( ! dazzle_is_off( $dazzle_custom_style )
						? ' post_layout_' . esc_attr( $dazzle_custom_style )
							. ' post_layout_' . esc_attr( $dazzle_custom_style ) . '_' . esc_attr( $dazzle_columns )
						: ''
						)
		);
	dazzle_add_blog_animation( $dazzle_template_args );
	?>
>
	<?php
	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}
	// Custom layout
	do_action( 'dazzle_action_show_layout', $dazzle_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $dazzle_template_args['slider'] ) || $dazzle_columns > 1 || ! dazzle_is_off( $dazzle_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
