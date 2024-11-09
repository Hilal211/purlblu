<?php
/**
 * The Classic template to display the content
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
$dazzle_expanded   = ! dazzle_sidebar_present() && dazzle_get_theme_option( 'expand_content' ) == 'expand';

$dazzle_post_format = get_post_format();
$dazzle_post_format = empty( $dazzle_post_format ) ? 'standard' : str_replace( 'post-format-', '', $dazzle_post_format );

?><div class="<?php
	if ( ! empty( $dazzle_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( dazzle_is_blog_style_use_masonry( $dazzle_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $dazzle_columns ) : esc_attr( $dazzle_columns_class ) );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $dazzle_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $dazzle_columns )
				. ' post_layout_' . esc_attr( $dazzle_blog_style[0] )
				. ' post_layout_' . esc_attr( $dazzle_blog_style[0] ) . '_' . esc_attr( $dazzle_columns )
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

	// Featured image
	$dazzle_hover      = ! empty( $dazzle_template_args['hover'] ) && ! dazzle_is_inherit( $dazzle_template_args['hover'] )
							? $dazzle_template_args['hover']
							: dazzle_get_theme_option( 'image_hover' );

	$dazzle_components = ! empty( $dazzle_template_args['meta_parts'] )
							? ( is_array( $dazzle_template_args['meta_parts'] )
								? $dazzle_template_args['meta_parts']
								: explode( ',', $dazzle_template_args['meta_parts'] )
								)
							: dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'meta_parts' ) );

	dazzle_show_post_featured( apply_filters( 'dazzle_filter_args_featured',
		array(
			'thumb_size' => ! empty( $dazzle_template_args['thumb_size'] )
				? $dazzle_template_args['thumb_size']
				: dazzle_get_thumb_size(
					'classic' == $dazzle_blog_style[0]
						? ( strpos( dazzle_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $dazzle_columns > 2 ? 'big' : 'huge' )
								: ( $dazzle_columns > 2
									? ( $dazzle_expanded ? 'square' : 'square' )
									: ($dazzle_columns > 1 ? 'square' : ( $dazzle_expanded ? 'huge' : 'big' ))
									)
							)
						: ( strpos( dazzle_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $dazzle_columns > 2 ? 'masonry-big' : 'full' )
								: ($dazzle_columns === 1 ? ( $dazzle_expanded ? 'huge' : 'big' ) : ( $dazzle_columns <= 2 && $dazzle_expanded ? 'masonry-big' : 'masonry' ))
							)
			),
			'hover'      => $dazzle_hover,
			'meta_parts' => $dazzle_components,
			'no_links'   => ! empty( $dazzle_template_args['no_links'] ),
        ),
        'content-classic',
        $dazzle_template_args
    ) );

	// Title and post meta
	$dazzle_show_title = get_the_title() != '';
	$dazzle_show_meta  = count( $dazzle_components ) > 0 && ! in_array( $dazzle_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $dazzle_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php

			// Post meta
			if ( apply_filters( 'dazzle_filter_show_blog_meta', $dazzle_show_meta, $dazzle_components, 'classic' ) ) {
				if ( count( $dazzle_components ) > 0 ) {
					do_action( 'dazzle_action_before_post_meta' );
					dazzle_show_post_meta(
						apply_filters(
							'dazzle_filter_post_meta_args', array(
							'components' => join( ',', $dazzle_components ),
							'seo'        => false,
							'echo'       => true,
						), $dazzle_blog_style[0], $dazzle_columns
						)
					);
					do_action( 'dazzle_action_after_post_meta' );
				}
			}

			// Post title
			if ( apply_filters( 'dazzle_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'dazzle_action_before_post_title' );
				if ( empty( $dazzle_template_args['no_links'] ) ) {
					the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
				} else {
					the_title( '<h4 class="post_title entry-title">', '</h4>' );
				}
				do_action( 'dazzle_action_after_post_title' );
			}

			if( !in_array( $dazzle_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
				// More button
				if ( apply_filters( 'dazzle_filter_show_blog_readmore', ! $dazzle_show_title || ! empty( $dazzle_template_args['more_button'] ), 'classic' ) ) {
					if ( empty( $dazzle_template_args['no_links'] ) ) {
						do_action( 'dazzle_action_before_post_readmore' );
						dazzle_show_post_more_link( $dazzle_template_args, '<div class="more-wrap">', '</div>' );
						do_action( 'dazzle_action_after_post_readmore' );
					}
				}
			}
			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
	if( in_array( $dazzle_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
		ob_start();
		if (apply_filters('dazzle_filter_show_blog_excerpt', empty($dazzle_template_args['hide_excerpt']) && dazzle_get_theme_option('excerpt_length') > 0, 'classic')) {
			dazzle_show_post_content($dazzle_template_args, '<div class="post_content_inner">', '</div>');
		}
		// More button
		if(! empty( $dazzle_template_args['more_button'] )) {
			if ( empty( $dazzle_template_args['no_links'] ) ) {
				do_action( 'dazzle_action_before_post_readmore' );
				dazzle_show_post_more_link( $dazzle_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'dazzle_action_after_post_readmore' );
			}
		}
		$dazzle_content = ob_get_contents();
		ob_end_clean();
		dazzle_show_layout($dazzle_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
	}
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
