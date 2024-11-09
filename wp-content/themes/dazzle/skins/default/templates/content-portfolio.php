<?php
/**
 * The Portfolio template to display the content
 *
 * Used for index/archive/search.
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

$dazzle_template_args = get_query_var( 'dazzle_template_args' );
if ( is_array( $dazzle_template_args ) ) {
	$dazzle_columns    = empty( $dazzle_template_args['columns'] ) ? 2 : max( 1, $dazzle_template_args['columns'] );
	$dazzle_blog_style = array( $dazzle_template_args['type'], $dazzle_columns );
    $dazzle_columns_class = dazzle_get_column_class( 1, $dazzle_columns, ! empty( $dazzle_template_args['columns_tablet']) ? $dazzle_template_args['columns_tablet'] : '', ! empty($dazzle_template_args['columns_mobile']) ? $dazzle_template_args['columns_mobile'] : '' );
} else {
	$dazzle_template_args = array();
	$dazzle_blog_style = explode( '_', dazzle_get_theme_option( 'blog_style' ) );
	$dazzle_columns    = empty( $dazzle_blog_style[1] ) ? 2 : max( 1, $dazzle_blog_style[1] );
    $dazzle_columns_class = dazzle_get_column_class( 1, $dazzle_columns );
}

$dazzle_post_format = get_post_format();
$dazzle_post_format = empty( $dazzle_post_format ) ? 'standard' : str_replace( 'post-format-', '', $dazzle_post_format );

?><div class="
<?php
if ( ! empty( $dazzle_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( dazzle_is_blog_style_use_masonry( $dazzle_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $dazzle_columns ) : esc_attr( $dazzle_columns_class ));
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $dazzle_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $dazzle_columns )
		. ( 'portfolio' != $dazzle_blog_style[0] ? ' ' . esc_attr( $dazzle_blog_style[0] )  . '_' . esc_attr( $dazzle_columns ) : '' )
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

	$dazzle_hover   = ! empty( $dazzle_template_args['hover'] ) && ! dazzle_is_inherit( $dazzle_template_args['hover'] )
								? $dazzle_template_args['hover']
								: dazzle_get_theme_option( 'image_hover' );

	if ( 'dots' == $dazzle_hover ) {
		$dazzle_post_link = empty( $dazzle_template_args['no_links'] )
								? ( ! empty( $dazzle_template_args['link'] )
									? $dazzle_template_args['link']
									: get_permalink()
									)
								: '';
		$dazzle_target    = ! empty( $dazzle_post_link ) && false === strpos( $dazzle_post_link, home_url() )
								? ' target="_blank" rel="nofollow"'
								: '';
	}
	
	// Meta parts
	$dazzle_components = ! empty( $dazzle_template_args['meta_parts'] )
							? ( is_array( $dazzle_template_args['meta_parts'] )
								? $dazzle_template_args['meta_parts']
								: explode( ',', $dazzle_template_args['meta_parts'] )
								)
							: dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'meta_parts' ) );

	// Featured image
	dazzle_show_post_featured( apply_filters( 'dazzle_filter_args_featured',
        array(
			'hover'         => $dazzle_hover,
			'no_links'      => ! empty( $dazzle_template_args['no_links'] ),
			'thumb_size'    => ! empty( $dazzle_template_args['thumb_size'] )
								? $dazzle_template_args['thumb_size']
								: dazzle_get_thumb_size(
									dazzle_is_blog_style_use_masonry( $dazzle_blog_style[0] )
										? (	strpos( dazzle_get_theme_option( 'body_style' ), 'full' ) !== false || $dazzle_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( dazzle_get_theme_option( 'body_style' ), 'full' ) !== false || $dazzle_columns < 3
											? 'square'
											: 'square'
											)
								),
			'thumb_bg' => dazzle_is_blog_style_use_masonry( $dazzle_blog_style[0] ) ? false : true,
			'show_no_image' => true,
			'meta_parts'    => $dazzle_components,
			'class'         => 'dots' == $dazzle_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $dazzle_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $dazzle_post_link )
												? '<a href="' . esc_url( $dazzle_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $dazzle_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
            'thumb_ratio'   => 'info' == $dazzle_hover ?  '100:102' : '',
        ),
        'content-portfolio',
        $dazzle_template_args
    ) );
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!