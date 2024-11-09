<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package DAZZLE
 * @since DAZZLE 1.71.0
 */

$dazzle_template_args = get_query_var( 'dazzle_template_args' );
if ( ! is_array( $dazzle_template_args ) ) {
	$dazzle_template_args = array(
								'type'    => 'band',
								'columns' => 1
								);
}

$dazzle_columns       = 1;

$dazzle_expanded      = ! dazzle_sidebar_present() && dazzle_get_theme_option( 'expand_content' ) == 'expand';

$dazzle_post_format   = get_post_format();
$dazzle_post_format   = empty( $dazzle_post_format ) ? 'standard' : str_replace( 'post-format-', '', $dazzle_post_format );

if ( is_array( $dazzle_template_args ) ) {
	$dazzle_columns    = empty( $dazzle_template_args['columns'] ) ? 1 : max( 1, $dazzle_template_args['columns'] );
	$dazzle_blog_style = array( $dazzle_template_args['type'], $dazzle_columns );
	if ( ! empty( $dazzle_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $dazzle_columns > 1 ) {
	    $dazzle_columns_class = dazzle_get_column_class( 1, $dazzle_columns, ! empty( $dazzle_template_args['columns_tablet']) ? $dazzle_template_args['columns_tablet'] : '', ! empty($dazzle_template_args['columns_mobile']) ? $dazzle_template_args['columns_mobile'] : '' );
				?><div class="<?php echo esc_attr( $dazzle_columns_class ); ?>"><?php
	}
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $dazzle_post_format ) );
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
								: array_map( 'trim', explode( ',', $dazzle_template_args['meta_parts'] ) )
								)
							: dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'meta_parts' ) );
	dazzle_show_post_featured( apply_filters( 'dazzle_filter_args_featured',
		array(
			'no_links'   => ! empty( $dazzle_template_args['no_links'] ),
			'hover'      => $dazzle_hover,
			'meta_parts' => $dazzle_components,
			'thumb_bg'   => true,
			'thumb_ratio'   => '1:1',
			'thumb_size' => ! empty( $dazzle_template_args['thumb_size'] )
								? $dazzle_template_args['thumb_size']
								: dazzle_get_thumb_size( 
								in_array( $dazzle_post_format, array( 'gallery', 'audio', 'video' ) )
									? ( strpos( dazzle_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: ( $dazzle_expanded 
											? 'big' 
											: 'medium-square'
											)
										)
									: 'masonry-big'
								)
		),
		'content-band',
		$dazzle_template_args
	) );

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$dazzle_show_title = get_the_title() != '';
		$dazzle_show_meta  = count( $dazzle_components ) > 0 && ! in_array( $dazzle_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $dazzle_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'dazzle_filter_show_blog_categories', $dazzle_show_meta && in_array( 'categories', $dazzle_components ), array( 'categories' ), 'band' ) ) {
					do_action( 'dazzle_action_before_post_category' );
					?>
					<div class="post_category">
						<?php
						dazzle_show_post_meta( apply_filters(
															'dazzle_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																'cat_sep'    => false,
																),
															'hover_' . $dazzle_hover, 1
															)
											);
						?>
					</div>
					<?php
					$dazzle_components = dazzle_array_delete_by_value( $dazzle_components, 'categories' );
					do_action( 'dazzle_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'dazzle_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'dazzle_action_before_post_title' );
					if ( empty( $dazzle_template_args['no_links'] ) ) {
						the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
					} else {
						the_title( '<h4 class="post_title entry-title">', '</h4>' );
					}
					do_action( 'dazzle_action_after_post_title' );
				}
				?>
			</div><!-- .post_header -->
			<?php
		}

		// Post content
		if ( ! isset( $dazzle_template_args['excerpt_length'] ) && ! in_array( $dazzle_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$dazzle_template_args['excerpt_length'] = 13;
		}
		if ( apply_filters( 'dazzle_filter_show_blog_excerpt', empty( $dazzle_template_args['hide_excerpt'] ) && dazzle_get_theme_option( 'excerpt_length' ) > 0, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				dazzle_show_post_content( $dazzle_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div><!-- .entry-content -->
			<?php
		}
		// Post meta
		if ( apply_filters( 'dazzle_filter_show_blog_meta', $dazzle_show_meta, $dazzle_components, 'band' ) ) {
			if ( count( $dazzle_components ) > 0 ) {
				do_action( 'dazzle_action_before_post_meta' );
				dazzle_show_post_meta(
					apply_filters(
						'dazzle_filter_post_meta_args', array(
							'components' => join( ',', $dazzle_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'dazzle_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'dazzle_filter_show_blog_readmore', ! $dazzle_show_title || ! empty( $dazzle_template_args['more_button'] ), 'band' ) ) {
			if ( empty( $dazzle_template_args['no_links'] ) ) {
				do_action( 'dazzle_action_before_post_readmore' );
				dazzle_show_post_more_link( $dazzle_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'dazzle_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $dazzle_template_args ) ) {
	if ( ! empty( $dazzle_template_args['slider'] ) || $dazzle_columns > 1 ) {
		?>
		</div>
		<?php
	}
}