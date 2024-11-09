<?php
/**
 * The template to display single post
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

// Full post loading
$full_post_loading          = dazzle_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = dazzle_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = dazzle_get_theme_option( 'posts_navigation_scroll_which_block' );

// Position of the related posts
$dazzle_related_position   = dazzle_get_theme_option( 'related_position' );

// Type of the prev/next post navigation
$dazzle_posts_navigation   = dazzle_get_theme_option( 'posts_navigation' );
$dazzle_prev_post          = false;
$dazzle_prev_post_same_cat = dazzle_get_theme_option( 'posts_navigation_scroll_same_cat' );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( dazzle_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	dazzle_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'dazzle_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $dazzle_posts_navigation ) {
		$dazzle_prev_post = get_previous_post( $dazzle_prev_post_same_cat );  // Get post from same category
		if ( ! $dazzle_prev_post && $dazzle_prev_post_same_cat ) {
			$dazzle_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $dazzle_prev_post ) {
			$dazzle_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $dazzle_prev_post ) ) {
		dazzle_sc_layouts_showed( 'featured', false );
		dazzle_sc_layouts_showed( 'title', false );
		dazzle_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $dazzle_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'dazzle_filter_get_template_part', 'templates/content', 'single-' . dazzle_get_theme_option( 'single_style' ) ), 'single-' . dazzle_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $dazzle_related_position, 'inside' ) === 0 ) {
		$dazzle_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'dazzle_action_related_posts' );
		$dazzle_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $dazzle_related_content ) ) {
			$dazzle_related_position_inside = max( 0, min( 9, dazzle_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $dazzle_related_position_inside ) {
				$dazzle_related_position_inside = mt_rand( 1, 9 );
			}

			$dazzle_p_number         = 0;
			$dazzle_related_inserted = false;
			$dazzle_in_block         = false;
			$dazzle_content_start    = strpos( $dazzle_content, '<div class="post_content' );
			$dazzle_content_end      = strrpos( $dazzle_content, '</div>' );

			for ( $i = max( 0, $dazzle_content_start ); $i < min( strlen( $dazzle_content ) - 3, $dazzle_content_end ); $i++ ) {
				if ( $dazzle_content[ $i ] != '<' ) {
					continue;
				}
				if ( $dazzle_in_block ) {
					if ( strtolower( substr( $dazzle_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$dazzle_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $dazzle_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $dazzle_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$dazzle_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $dazzle_content[ $i + 1 ] && in_array( $dazzle_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$dazzle_p_number++;
					if ( $dazzle_related_position_inside == $dazzle_p_number ) {
						$dazzle_related_inserted = true;
						$dazzle_content = ( $i > 0 ? substr( $dazzle_content, 0, $i ) : '' )
											. $dazzle_related_content
											. substr( $dazzle_content, $i );
					}
				}
			}
			if ( ! $dazzle_related_inserted ) {
				if ( $dazzle_content_end > 0 ) {
					$dazzle_content = substr( $dazzle_content, 0, $dazzle_content_end ) . $dazzle_related_content . substr( $dazzle_content, $dazzle_content_end );
				} else {
					$dazzle_content .= $dazzle_related_content;
				}
			}
		}

		dazzle_show_layout( $dazzle_content );
	}

	// Comments
	do_action( 'dazzle_action_before_comments' );
	comments_template();
	do_action( 'dazzle_action_after_comments' );

	// Related posts
	if ( 'below_content' == $dazzle_related_position
		&& ( 'scroll' != $dazzle_posts_navigation || dazzle_get_theme_option( 'posts_navigation_scroll_hide_related' ) == 0 )
		&& ( ! $full_post_loading || dazzle_get_theme_option( 'open_full_post_hide_related' ) == 0 )
	) {
		do_action( 'dazzle_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $dazzle_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $dazzle_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $dazzle_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $dazzle_prev_post ) ); ?>"
			<?php do_action( 'dazzle_action_nav_links_single_scroll_data', $dazzle_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();
