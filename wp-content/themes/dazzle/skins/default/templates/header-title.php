<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

// Page (category, tag, archive, author) title

if ( dazzle_need_page_title() ) {
	dazzle_sc_layouts_showed( 'title', true );
	dazzle_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								dazzle_show_post_meta(
									apply_filters(
										'dazzle_filter_post_meta_args', array(
											'components' => join( ',', dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', dazzle_array_get_keys_by_value( dazzle_get_theme_option( 'counters' ) ) ),
											'seo'        => dazzle_is_on( dazzle_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$dazzle_blog_title           = dazzle_get_blog_title();
							$dazzle_blog_title_text      = '';
							$dazzle_blog_title_class     = '';
							$dazzle_blog_title_link      = '';
							$dazzle_blog_title_link_text = '';
							if ( is_array( $dazzle_blog_title ) ) {
								$dazzle_blog_title_text      = $dazzle_blog_title['text'];
								$dazzle_blog_title_class     = ! empty( $dazzle_blog_title['class'] ) ? ' ' . $dazzle_blog_title['class'] : '';
								$dazzle_blog_title_link      = ! empty( $dazzle_blog_title['link'] ) ? $dazzle_blog_title['link'] : '';
								$dazzle_blog_title_link_text = ! empty( $dazzle_blog_title['link_text'] ) ? $dazzle_blog_title['link_text'] : '';
							} else {
								$dazzle_blog_title_text = $dazzle_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $dazzle_blog_title_class ); ?>">
								<?php
								$dazzle_top_icon = dazzle_get_term_image_small();
								if ( ! empty( $dazzle_top_icon ) ) {
									$dazzle_attr = dazzle_getimagesize( $dazzle_top_icon );
									?>
									<img src="<?php echo esc_url( $dazzle_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'dazzle' ); ?>"
										<?php
										if ( ! empty( $dazzle_attr[3] ) ) {
											dazzle_show_layout( $dazzle_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $dazzle_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $dazzle_blog_title_link ) && ! empty( $dazzle_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $dazzle_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $dazzle_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'dazzle_action_breadcrumbs' );
						$dazzle_breadcrumbs = ob_get_contents();
						ob_end_clean();
						dazzle_show_layout( $dazzle_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
