<?php
/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets
 *
 * @package DAZZLE
 * @since DAZZLE 1.0
 */

if ( ! defined( 'DAZZLE_THEME_DIR' ) ) {
	define( 'DAZZLE_THEME_DIR', trailingslashit( get_template_directory() ) );
}
if ( ! defined( 'DAZZLE_THEME_URL' ) ) {
	define( 'DAZZLE_THEME_URL', trailingslashit( get_template_directory_uri() ) );
}
if ( ! defined( 'DAZZLE_CHILD_DIR' ) ) {
	define( 'DAZZLE_CHILD_DIR', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'DAZZLE_CHILD_URL' ) ) {
	define( 'DAZZLE_CHILD_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

//-------------------------------------------------------
//-- Theme init
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'dazzle_theme_setup1', 1 );
	/**
	 * Load a text domain before all other actions.
	 *
	 * Theme-specific init actions order:
	 *
	 * Action 'after_setup_theme':
	 *
	 * 1 - register filters to add/remove items to the lists used in the Theme Options
	 *
	 * 2 - create the Theme Options
	 *
	 * 3 - add/remove elements to the Theme Options
	 *
	 * 5 - load the Theme Options. Attention! After this step you can use only basic options (not overriden options)
	 *
	 * 9 - register other filters (for installer, etc.)
	 *
	 * 10 - all other (standard) Theme init procedures (not ordered)
	 *
	 * Action 'wp_loaded'
	 *
	 * 1 - detect an override mode. Attention! Only after this step you can use overriden options
	 *     (separate values for the Blog, Shop, Team, Courses, etc.)
	 */
	function dazzle_theme_setup1() {
		// Make theme available for translation
		// Translations can be filed in the /languages directory
		// Attention! Translations must be loaded before first call any translation functions!
		load_theme_textdomain( 'dazzle', dazzle_get_folder_dir( 'languages' ) );
	}
}

if ( ! function_exists( 'dazzle_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'dazzle_theme_setup9', 9 );
	/**
	 * A general theme setup: add a theme supports, navigation menus, hooks for other actions and filters.
	 */
	function dazzle_theme_setup9() {

		// Set theme content width
		$GLOBALS['content_width'] = apply_filters( 'dazzle_filter_content_width', (int)dazzle_get_theme_option( 'page_width' ) );

		// Theme support '-full' versions of styles and scripts (used in the editors)
		add_theme_support( 'styles-and-scripts-full-merged' );
		
		// Allow external updtates
		if ( DAZZLE_THEME_ALLOW_UPDATE ) {
			add_theme_support( 'theme-updates-allowed' );
		}

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Custom header setup
		add_theme_support( 'custom-header',
			array(
				'header-text' => false,
				'video'       => true,
			)
		);

		// Custom logo
		add_theme_support( 'custom-logo',
			array(
				'width'       => 250,
				'height'      => 60,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		// Custom backgrounds setup
		add_theme_support( 'custom-background', array() );

		// Partial refresh support in the Customize
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Supported posts formats
		add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat' ) );

		// Autogenerate title tag
		add_theme_support( 'title-tag' );

		// Add theme menus
		add_theme_support( 'nav-menus' );

		// Switch default markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		// Register navigation menu
		register_nav_menus(
			array(
				'menu_main'   => esc_html__( 'Main Menu', 'dazzle' ),
				'menu_mobile' => esc_html__( 'Mobile Menu', 'dazzle' ),
				'menu_footer' => esc_html__( 'Footer Menu', 'dazzle' ),
			)
		);

		// Register theme-specific thumb sizes
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 370, 0, false );
		$thumb_sizes = dazzle_storage_get( 'theme_thumbs' );
		$mult        = dazzle_get_theme_option( 'retina_ready', 1 );
		if ( $mult > 1 ) {
			$GLOBALS['content_width'] = apply_filters( 'dazzle_filter_content_width', (int)dazzle_get_theme_option( 'page_width' ) * $mult );
		}
		foreach ( $thumb_sizes as $k => $v ) {
			add_image_size( $k, $v['size'][0], $v['size'][1], $v['size'][2] );
			if ( $mult > 1 ) {
				add_image_size( $k . '-@retina', $v['size'][0] * $mult, $v['size'][1] * $mult, $v['size'][2] );
			}
		}
		// Add new thumb names
		add_filter( 'image_size_names_choose', 'dazzle_theme_thumbs_sizes' );

		// Excerpt filters
		add_filter( 'excerpt_length', 'dazzle_excerpt_length' );
		add_filter( 'excerpt_more', 'dazzle_excerpt_more' );

		// Comment form
		add_filter( 'comment_form_fields', 'dazzle_comment_form_fields' );
		add_filter( 'comment_form_fields', 'dazzle_comment_form_agree', 11 );

		// Add required meta tags in the head
		add_action( 'wp_head', 'dazzle_wp_head', 0 );

		// Load current page/post customization (if present)
		add_action( 'wp_footer', 'dazzle_wp_footer' );
		add_action( 'admin_footer', 'dazzle_wp_footer' );

		// Enqueue scripts and styles for the frontend
		add_action( 'wp_enqueue_scripts', 'dazzle_load_theme_fonts', 0 );
		add_action( 'wp_enqueue_scripts', 'dazzle_load_theme_icons', 0 );
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles', 1000 );                  // priority 1000 - load main theme styles
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_single', 1020);            // priority 1020 - load styles of single posts
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_plugins', 1100 );          // priority 1100 - load styles of the supported plugins
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_custom', 1200 );           // priority 1200 - load styles with custom fonts and colors
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_child', 1500 );            // priority 1500 - load styles of the child theme
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_responsive', 2000 );       // priority 2000 - load responsive styles after all other styles
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_single_responsive', 2020); // priority 2020 - load responsive styles of single posts after all other styles
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_styles_responsive_child', 2500);  // priority 2500 - load responsive styles of the child theme after all other responsive styles

		// Enqueue scripts for the frontend
		add_action( 'wp_enqueue_scripts', 'dazzle_wp_scripts', 1000 );                 // priority 1000 - load main theme scripts
		add_action( 'wp_footer', 'dazzle_localize_scripts' );

		// Add body classes
		add_filter( 'body_class', 'dazzle_add_body_classes' );

		// Register sidebars
		add_action( 'widgets_init', 'dazzle_register_sidebars' );
	}
}


//-------------------------------------------------------
//-- Theme styles
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_theme_fonts' ) ) {
	/**
	 * Load a theme-specific fonts at priority 0, because the font styles must be loaded before a main stylesheet.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_load_theme_fonts', 0);
	 */
	function dazzle_load_theme_fonts() {
		$links = dazzle_theme_fonts_links();
		if ( count( $links ) > 0 ) {
			foreach ( $links as $slug => $link ) {
				wp_enqueue_style( sprintf( 'dazzle-font-%s', $slug ), $link, array(), null );
			}
		}
	}
}

if ( ! function_exists( 'dazzle_load_theme_icons' ) ) {
	/**
	 * Load a theme-specific font icons at priority 0, because the icon styles must be loaded before a main stylesheet.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_load_theme_icons', 0);
	 */
	function dazzle_load_theme_icons() {
		// This style NEED the theme prefix, because style 'fontello' in some plugin contain different set of characters
		// and can't be used instead this style!
		wp_enqueue_style( 'dazzle-fontello', dazzle_get_file_url( 'css/font-icons/css/fontello.css' ), array(), null );
	}
}

if ( ! function_exists( 'dazzle_wp_styles' ) ) {
	/**
	 * Load a main theme styles for the frontend.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles', 1000);
	 */
	function dazzle_wp_styles() {

		// Load main stylesheet
		$main_stylesheet = DAZZLE_THEME_URL . 'style.css';
		wp_enqueue_style( 'dazzle-style', $main_stylesheet, array(), null );

		// Add custom bg image
		$bg_image = dazzle_remove_protocol_from_url( dazzle_get_theme_option( 'front_page_bg_image' ), false );
		if ( is_front_page() && ! empty( $bg_image ) && dazzle_is_on( dazzle_get_theme_option( 'front_page_enabled', false ) ) ) {
			// Add custom bg image for the Front page
			dazzle_add_inline_css( 'body.frontpage, body.home-page, body.home { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
		} else {
			// Add custom bg image for the body_style == 'boxed'
			$bg_image = dazzle_get_theme_option( 'boxed_bg_image' );
			if ( ! empty( $bg_image ) && ( dazzle_get_theme_option( 'body_style' ) == 'boxed' || is_customize_preview() ) ) {
				dazzle_add_inline_css( '.body_style_boxed { background-image:url(' . esc_url( dazzle_clear_thumb_size( $bg_image ) ) . ') !important }' );
			}
		}

		// Add post nav background
		dazzle_add_bg_in_post_nav();
	}
}

if ( ! function_exists( 'dazzle_wp_styles_single' ) ) {
	/**
	 * Load styles for single posts.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_single', 1020);
	 */
	function dazzle_wp_styles_single() {
		if ( apply_filters( 'dazzle_filters_separate_single_styles', false )
			&& apply_filters( 'dazzle_filters_load_single_styles', dazzle_is_single() || dazzle_is_singular( 'attachment' ) || (int) dazzle_get_theme_option( 'open_full_post_in_blog' ) > 0 )
		) {
			if ( dazzle_is_off( dazzle_get_theme_option( 'debug_mode' ) ) ) {
				$file = dazzle_get_file_url( 'css/__single.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'dazzle-single', $file, array(), null );
				}
			} else {
				$file = dazzle_get_file_url( 'css/single.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'dazzle-single', $file, array(), null );
				}
			}
		}
	}
}

if ( ! function_exists( 'dazzle_wp_styles_plugins' ) ) {
	/**
	 * Load styles for all supported plugins.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_plugins', 1100);
	 */
	function dazzle_wp_styles_plugins() {
		if ( dazzle_is_off( dazzle_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'dazzle-plugins', dazzle_get_file_url( 'css/__plugins' . ( dazzle_is_preview() || ! dazzle_optimize_css_and_js_loading() ? '-full' : '' ) . '.css' ), array(), null );
		}
	}
}

if ( ! function_exists( 'dazzle_wp_styles_custom' ) ) {
	/**
	 * Load styles with CSS variables to set up a theme-specific custom fonts and colors.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_custom', 1200);
	 */
	function dazzle_wp_styles_custom() {
		if ( ! is_customize_preview() && dazzle_is_off( dazzle_get_theme_option( 'debug_mode' ) ) && ! dazzle_is_blog_mode_custom() ) {
			wp_enqueue_style( 'dazzle-custom', dazzle_get_file_url( 'css/__custom.css' ), array(), null );
		} else {
			wp_enqueue_style( 'dazzle-custom', dazzle_get_file_url( 'css/__custom-inline.css' ), array(), null );
			wp_add_inline_style( 'dazzle-custom', dazzle_customizer_get_css() );
		}
	}
}

if ( ! function_exists( 'dazzle_wp_styles_responsive' ) ) {
	/**
	 * Load a theme responsive styles (a priority 2000 is used to load it after the main styles and plugins custom styles)
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_responsive', 2000);
	 */
	function dazzle_wp_styles_responsive() {
		if ( dazzle_is_off( dazzle_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'dazzle-responsive', dazzle_get_file_url( 'css/__responsive' . ( dazzle_is_preview() || ! dazzle_optimize_css_and_js_loading() ? '-full' : '' ) . '.css' ), array(), null, dazzle_media_for_load_css_responsive( 'main' ) );
		} else {
			wp_enqueue_style( 'dazzle-responsive', dazzle_get_file_url( 'css/responsive.css' ), array(), null, dazzle_media_for_load_css_responsive( 'main' ) );
		}
	}
}

if ( ! function_exists( 'dazzle_wp_styles_single_responsive' ) ) {
	/**
	 * Load a theme responsive styles for single posts (a priority 2020 is used to load it after the main and plugins responsive styles).
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_single_responsive', 2020);
	 */
	function dazzle_wp_styles_single_responsive() {
		if ( apply_filters( 'dazzle_filters_separate_single_styles', false )
			&& apply_filters( 'dazzle_filters_load_single_styles', dazzle_is_single() || dazzle_is_singular( 'attachment' ) || (int) dazzle_get_theme_option( 'open_full_post_in_blog' ) > 0 )
		) {
			if ( dazzle_is_off( dazzle_get_theme_option( 'debug_mode' ) ) ) {
				$file = dazzle_get_file_url( 'css/__single-responsive.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'dazzle-single-responsive', $file, array(), null, dazzle_media_for_load_css_responsive( 'single' ) );
				}
			} else {
				$file = dazzle_get_file_url( 'css/single-responsive.css' );
				if ( ! empty( $file ) ) {
					wp_enqueue_style( 'dazzle-single-responsive', $file, array(), null, dazzle_media_for_load_css_responsive( 'single' ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'dazzle_wp_styles_child' ) ) {
	/**
	 * Load a child-theme stylesheet after all theme styles (if child-theme folder is not equal to the theme folder).
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_child', 1500);
	 */
	function dazzle_wp_styles_child() {
		if ( DAZZLE_THEME_URL != DAZZLE_CHILD_URL ) {
			wp_enqueue_style( 'dazzle-child', DAZZLE_CHILD_URL . 'style.css', array( 'dazzle-style' ), null );
		}
	}
}

if ( ! function_exists( 'dazzle_wp_styles_responsive_child' ) ) {
	/**
	 * Load a child-theme responsive styles (a priority 2500 is used to load it after other responsive styles
	 * and after the child-theme stylesheet)
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_styles_responsive_child', 2500);
	 */
	function dazzle_wp_styles_responsive_child() {
		if ( DAZZLE_THEME_URL != DAZZLE_CHILD_URL && file_exists( DAZZLE_CHILD_DIR . 'responsive.css' ) ) {
			wp_enqueue_style( 'dazzle-responsive-child', DAZZLE_CHILD_URL . 'responsive.css', array( 'dazzle-responsive' ), null, dazzle_media_for_load_css_responsive( 'main' ) );
		}
	}
}

if ( ! function_exists( 'dazzle_media_for_load_css_responsive' ) ) {
	/**
	 * Return a 'media' descriptor for the tag 'link' to load responsive CSS only on devices where they are really needed.
	 *
	 * @param string $slug   Optional. A slug of responsive CSS. Default is 'main'.
	 * @param string $media  Optional. A default media descriptor. Default is 'all'.
	 *
	 * @return string        A media descriptor corresponding to the specified slug.
	 */
	function dazzle_media_for_load_css_responsive( $slug = 'main', $media = 'all' ) {
		global $DAZZLE_STORAGE;
		$condition = 'all';
		$media = apply_filters( 'dazzle_filter_media_for_load_css_responsive', $media, $slug );
		if ( ! empty( $DAZZLE_STORAGE['responsive'][ $media ]['max'] ) ) {
			$condition = sprintf( '(max-width:%dpx)', $DAZZLE_STORAGE['responsive'][ $media ]['max'] );
		} 
		return apply_filters( 'dazzle_filter_condition_for_load_css_responsive', $condition, $slug );
	}
}

if ( ! function_exists( 'dazzle_media_for_load_css_responsive_callback' ) ) {
	add_filter( 'dazzle_filter_media_for_load_css_responsive', 'dazzle_media_for_load_css_responsive_callback', 10, 2 );
	/**
	 * Return a maximum 'media' slug to use as a default value for all responsive css-files
	 * (if corresponding media is not detected by a specified slug).
	 *
	 * Hooks: add_filter( 'dazzle_filter_media_for_load_css_responsive', 'dazzle_media_for_load_css_responsive_callback', 10, 2 );
	 *
	 * @param string $media  A current media descriptor.
	 * @param string $slug   A current slug to detect a media descriptor. Not used in this function.
	 *
	 * @return string        A default media descriptor, if media stay equal to 'all' after all previous hooks.
	 */
	function dazzle_media_for_load_css_responsive_callback( $media, $slug ) {
		return 'all' == $media ? 'xxl' : $media;
	}
}


//-------------------------------------------------------
//-- Theme scripts
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_wp_scripts' ) ) {
	/**
	 * Load a theme-specific scripts for the frontend.
	 *
	 * Hooks: add_action('wp_enqueue_scripts', 'dazzle_wp_scripts', 1000);
	 */
	function dazzle_wp_scripts() {
		$blog_archive = dazzle_storage_get( 'blog_archive' ) === true || is_home();
		$blog_style   = dazzle_get_theme_option( 'blog_style' );
		$use_masonry  = false;
		if ( strpos( $blog_style, 'blog-custom-' ) === 0 ) {
			$blog_id   = dazzle_get_custom_blog_id( $blog_style );
			$blog_meta = dazzle_get_custom_layout_meta( $blog_id );
			if ( ! empty( $blog_meta['scripts_required'] ) && ! dazzle_is_off( $blog_meta['scripts_required'] ) ) {
				$blog_style  = $blog_meta['scripts_required'];
				$use_masonry = strpos( $blog_meta['scripts_required'], 'masonry' ) !== false;
			}
		} else {
			$blog_parts  = explode( '_', $blog_style );
			$blog_style  = $blog_parts[0];
			$use_masonry = dazzle_is_blog_style_use_masonry( $blog_style );
		}

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', dazzle_get_file_url( 'js/superfish/superfish.min.js' ), array( 'jquery' ), null, true );

		// Background video
		$header_video = dazzle_get_header_video();
		if ( ! empty( $header_video ) && ! dazzle_is_inherit( $header_video ) ) {
			if ( dazzle_is_youtube_url( $header_video ) ) {
				wp_enqueue_script( 'jquery-tubular', dazzle_get_file_url( 'js/tubular/jquery.tubular.js' ), array( 'jquery' ), null, true );
			} else {
				wp_enqueue_script( 'bideo', dazzle_get_file_url( 'js/bideo/bideo.js' ), array(), null, true );
			}
		}

		// Merged scripts
		if ( dazzle_is_off( dazzle_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'dazzle-init', dazzle_get_file_url( 'js/__scripts' . ( dazzle_is_preview() || ! dazzle_optimize_css_and_js_loading() ? '-full' : '' ) . '.js' ), apply_filters( 'dazzle_filter_script_deps', array( 'jquery' ) ), null, true );
		} else {
			// Skip link focus
			wp_enqueue_script( 'skip-link-focus-fix', dazzle_get_file_url( 'js/skip-link-focus-fix/skip-link-focus-fix.js' ), null, true );
			// Theme scripts
			wp_enqueue_script( 'dazzle-utils', dazzle_get_file_url( 'js/utils.js' ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dazzle-init', dazzle_get_file_url( 'js/init.js' ), array( 'jquery' ), null, true );
		}

		// Load scripts for smooth parallax animation on the single post or on the post archive (if the option 'open_full_post_in_blog' is on)
		if ( dazzle_get_theme_option( 'single_parallax' ) != 0
			&& ( dazzle_is_singular( 'post' )
				|| ( (int) dazzle_get_theme_option( 'open_full_post_in_blog' ) > 0 && ( is_home() || is_archive() || is_category() ) )
				)
		) {
			dazzle_load_parallax_scripts();
		}

		// Load masonry scripts
		if ( ( $blog_archive && $use_masonry ) || ( dazzle_is_singular( 'post' ) && str_replace( 'post-format-', '', get_post_format() ) == 'gallery' ) ) {
			dazzle_load_masonry_scripts();
		}

		// Load tabs to show filters
		if ( $blog_archive && ! is_customize_preview() && ! dazzle_is_off( dazzle_get_theme_option( 'show_filters' ) ) ) {
			wp_enqueue_script( 'jquery-ui-tabs', false, array( 'jquery', 'jquery-ui-core' ), null, true );
		}

		// Comments
		if ( dazzle_is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Media elements library
		if ( dazzle_get_theme_setting( 'use_mediaelements' ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}
}

if ( ! function_exists( 'dazzle_localize_scripts' ) ) {
	/**
	 * Localize a theme-specific scripts: add variables to use in JS in the frontend.
	 *
	 * Trigger a filter 'dazzle_filter_localize_script' to allow other modules add their variables to the localization array.
	 *
	 * Hooks: add_action( 'wp_footer', 'dazzle_localize_scripts' );
	 */
	function dazzle_localize_scripts() {

		$video = dazzle_get_header_video();

		wp_localize_script( 'dazzle-init', 'DAZZLE_STORAGE', apply_filters( 'dazzle_filter_localize_script', array(
			// AJAX parameters
			'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
			'ajax_nonce'          => esc_attr( wp_create_nonce( admin_url( 'admin-ajax.php' ) ) ),

			// Site base url
			'site_url'            => esc_url( get_home_url() ),
			'theme_url'           => DAZZLE_THEME_URL,

			// Site color scheme
			'site_scheme'         => sprintf( 'scheme_%s', dazzle_get_theme_option( 'color_scheme' ) ),

			// User logged in
			'user_logged_in'      => is_user_logged_in() ? true : false,

			// Window width to switch the site header to the mobile layout
			'mobile_layout_width' => 768,
			'mobile_device'       => wp_is_mobile(),

			// Mobile breakpoints for JS (if window width less then)
			'mobile_breakpoint_underpanels_off' => 768,
			'mobile_breakpoint_fullheight_off' => 1025,

			// Sidemenu options
			'menu_side_stretch'   => (int) dazzle_get_theme_option( 'menu_side_stretch' ) > 0,
			'menu_side_icons'     => (int) dazzle_get_theme_option( 'menu_side_icons' ) > 0,

			// Video background
			'background_video'    => dazzle_is_from_uploads( $video ) ? $video : '',

			// Video and Audio tag wrapper
			'use_mediaelements'   => dazzle_get_theme_setting( 'use_mediaelements' ) ? true : false,

			// Resize video and iframe
			'resize_tag_video'    => false,
			'resize_tag_iframe'   => true,

			// Allow open full post in the blog
			'open_full_post'      => (int) dazzle_get_theme_option( 'open_full_post_in_blog' ) > 0,

			// Which block to load in the single posts
			'which_block_load'    => dazzle_get_theme_option( 'posts_navigation_scroll_which_block' ),

			// Current mode
			'admin_mode'          => false,

			// Strings for translation
			'msg_ajax_error'      => esc_html__( 'Invalid server answer!', 'dazzle' ),
			'msg_i_agree_error'   => esc_html__( 'Please accept the terms of our Privacy Policy.', 'dazzle' ),
		) ) );
	}
}

if ( ! function_exists( 'dazzle_load_masonry_scripts' ) ) {
	/**
	 * Enqueue a masonry scripts (if need for the current page).
	 */
	function dazzle_load_masonry_scripts() {
		static $once = true;
		if ( $once ) {
			$once = false;
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'masonry' );
		}
	}
}

if ( ! function_exists( 'dazzle_load_parallax_scripts' ) ) {
	/**
	 * Enqueue a parallax scripts (if need for the current page).
	 */
	function dazzle_load_parallax_scripts() {
		if ( function_exists( 'trx_addons_enqueue_parallax' ) ) {
			trx_addons_enqueue_parallax();
		}
	}
}

if ( ! function_exists( 'dazzle_load_specific_scripts' ) ) {
	add_filter( 'dazzle_filter_enqueue_blog_scripts', 'dazzle_load_specific_scripts', 10, 5 );
	/**
	 * Enqueue a blog-specific styles and scripts.
	 *
	 * Hooks: add_filter( 'dazzle_filter_enqueue_blog_scripts', 'dazzle_load_specific_scripts', 10, 5 );
	 *
	 * @param bool $load           A filterable flag indicating whether scripts should be loaded by default (true)
	 *                             or they are already loaded by one of the handlers (false).
	 * @param string $blog_style   A slug of the blog style.
	 * @param string $script_slug  A slug of the script to load.
	 * @param array|bool $list     A list with scripts to merge or false if called from enqueue_scripts.
	 * @param bool $responsive     If true - need to load responsive styles, else - a main styles and scripts.
	 *
	 * @return bool                A filtered flag indicating whether scripts should be loaded by default (true)
	 *                             or they are already loaded by one of the handlers (false).
	 */
	function dazzle_load_specific_scripts( $load, $blog_style, $script_slug, $list, $responsive ) {
		if ( 'masonry' == $script_slug && false === $list ) { // if list === false - called from enqueue_scripts, else - called from merge_script
			dazzle_load_masonry_scripts();
			$load = false;
		}
		return $load;
	}
}


//-------------------------------------------------------
//-- Head, body and footer
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_wp_head' ) ) {
	/**
	 * Add meta tags to the header for the frontend.
	 *
	 * Hooks: add_action( 'wp_head',	'dazzle_wp_head', 1 );
	 */
	function dazzle_wp_head() {
		if ( ! dazzle_gutenberg_is_fse_theme() ) {
			?>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<?php
		}
		// Add ', maximum-scale=1' to the content of the meta name 'viewport' to disallow the page scaling.
		?>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="format-detection" content="telephone=no">
		<link rel="profile" href="//gmpg.org/xfn/11">
		<?php
		if ( dazzle_is_singular() && pings_open() ) {
			?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
			<?php
		}
	}
}

if ( ! function_exists( 'dazzle_add_body_classes' ) ) {
	/**
	 * Add a theme-specific classes to the tag 'body'.
	 *
	 * Hooks: add_filter( 'body_class', 'dazzle_add_body_classes' );
	 *
	 * @param array $classes  An array with classes for the tag 'body'.
	 *
	 * @return array          A filtered array with a theme-specific classes for the tag 'body'.
	 */
	function dazzle_add_body_classes( $classes ) {

		$classes[] = 'scheme_' . esc_attr( dazzle_get_theme_option( 'color_scheme' ) );

		if ( is_customize_preview() ) {
			$classes[] = 'customize_preview';
		}

		$blog_mode = dazzle_storage_get( 'blog_mode' );
		$classes[] = 'blog_mode_' . esc_attr( $blog_mode );
		$classes[] = 'body_style_' . esc_attr( dazzle_get_theme_option( 'body_style' ) );

		if ( in_array( $blog_mode, array( 'post', 'page' ) ) || apply_filters( 'dazzle_filter_single_post_header', dazzle_is_singular( 'post' ) ) ) {
			$classes[] = 'is_single';
		} else {
			$classes[] = ' is_stream';
			$classes[] = 'blog_style_' . esc_attr( dazzle_get_theme_option( 'blog_style' ) );
			if ( dazzle_storage_get( 'blog_template' ) > 0 ) {
				$classes[] = 'blog_template';
			}
		}

		if ( apply_filters( 'dazzle_filter_single_post_header', dazzle_is_singular( 'post' ) || dazzle_is_singular( 'attachment' ) ) ) {
			$classes[] = 'single_style_' . esc_attr( dazzle_get_theme_option( 'single_style' ) );
		}

		if ( dazzle_sidebar_present() ) {
			$classes[] = 'sidebar_show sidebar_' . esc_attr( dazzle_get_theme_option( 'sidebar_position' ) );
			$classes[] = 'sidebar_small_screen_' . esc_attr( dazzle_get_theme_option( 'sidebar_position_ss' ) );
		} else {
			$expand = dazzle_get_theme_option( 'expand_content' );
			// Compatibility with old versions
			if ( "={$expand}" == '=0' ) {
				$expand = 'normal';
			} else if ( "={$expand}" == '=1' ) {
				$expand = 'expand';
			}
			if ( 'narrow' == $expand && ! dazzle_is_singular( apply_filters('dazzle_filter_is_singular_type', array('post') ) ) ) {
				$expand = 'normal';
			}
			$classes[] = 'sidebar_hide';
			$classes[] = "{$expand}_content";
		}

		if ( dazzle_is_on( dazzle_get_theme_option( 'remove_margins' ) ) ) {
			$classes[] = 'remove_margins';
		}

		$bg_image = dazzle_get_theme_option( 'front_page_bg_image' );
		if ( is_front_page() && ! empty( $bg_image ) && dazzle_is_on( dazzle_get_theme_option( 'front_page_enabled', false ) ) ) {
			$classes[] = 'with_bg_image';
		}

		$classes[] = 'trx_addons_' . esc_attr( dazzle_exists_trx_addons() ? 'present' : 'absent' );

		$classes[] = 'header_type_' . esc_attr( dazzle_get_theme_option( 'header_type' ) );
		$classes[] = 'header_style_' . esc_attr( 'default' == dazzle_get_theme_option( 'header_type' ) ? 'header-default' : dazzle_get_theme_option( 'header_style' ) );
		$header_position = dazzle_get_theme_option( 'header_position' );
		if ( 'over' == $header_position && dazzle_is_single() && ! has_post_thumbnail() ) {
			$header_position = 'default';
		}
		$classes[] = 'header_position_' . esc_attr( $header_position );

		$menu_side = dazzle_get_theme_option( 'menu_side' );
		$classes[] = 'menu_side_' . esc_attr( $menu_side ) . ( in_array( $menu_side, array( 'left', 'right' ) ) ? ' menu_side_present' : '' );
		$classes[] = 'no_layout';

		if ( dazzle_get_theme_setting( 'fixed_blocks_sticky' ) ) {
			$classes[] = 'fixed_blocks_sticky';
		}

		if ( dazzle_get_theme_option( 'blog_content' ) == 'fullpost' ) {
			$classes[] = 'fullpost_exist';
		}

		return $classes;
	}
}

if ( ! function_exists( 'dazzle_wp_footer' ) ) {
	/**
	 * Load a customization styles with css rules added while a current page built.
	 *
	 * Hooks: add_action('wp_footer', 'dazzle_wp_footer');
	 *        add_action('admin_footer', 'dazzle_wp_footer');
	 */
	function dazzle_wp_footer() {
		// Add header zoom
		$header_zoom = max( 0.2, min( 2, (float) dazzle_get_theme_option( 'header_zoom' ) ) );
		if ( 1 != $header_zoom ) {
			dazzle_add_inline_css( ".sc_layouts_title_title{font-size:{$header_zoom}em}" );
		}
		// Add logo zoom
		$logo_zoom = max( 0.2, min( 2, (float) dazzle_get_theme_option( 'logo_zoom' ) ) );
		if ( 1 != $logo_zoom ) {
			dazzle_add_inline_css( ".custom-logo-link,.sc_layouts_logo{font-size:{$logo_zoom}em}" );
		}
		// Put inline styles to the output
		$css = dazzle_get_inline_css();
		if ( ! empty( $css ) ) {
			// Attention! Don't change id in the tag 'style' - need to properly work the 'view more' script
			dazzle_show_layout( apply_filters( 'dazzle_filter_inline_css', $css ), '<style type="text/css" id="dazzle-inline-styles-inline-css">', '</style>' );
		}
	}
}


//-------------------------------------------------------
//-- Sidebars and widgets
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_register_sidebars' ) ) {
	/**
	 * Register a theme-specific widgetized areas.
	 *
	 * Hooks: add_action('widgets_init', 'dazzle_register_sidebars');
	 */
	function dazzle_register_sidebars() {
		$sidebars = dazzle_get_sidebars();
		if ( is_array( $sidebars ) && count( $sidebars ) > 0 ) {
			$cnt = 0;
			foreach ( $sidebars as $id => $sb ) {
				$cnt++;
				register_sidebar(
					apply_filters( 'dazzle_filter_register_sidebar',
						array(
							'name'          => $sb['name'],
							'description'   => $sb['description'],
							// Translators: Add the sidebar number to the id
							'id'            => ! empty( $id ) ? $id : sprintf( 'theme_sidebar_%d', $cnt),
							'before_widget' => '<aside class="widget %2$s">',	// %1$s - id, %2$s - class
							'after_widget'  => '</aside>',
							'before_title'  => '<h5 class="widget_title">',
							'after_title'   => '</h5>',
						)
					)
				);
			}
		}
	}
}

if ( ! function_exists( 'dazzle_get_sidebars' ) ) {
	/**
	 * Return a list with all theme-specific widgetized areas.
	 *
	 * @return array  A list of the widgetized areas in format:
	 *                [
	 *                  ['name' => 'Sidebar1 Name', 'description' => 'Sidebar1 Description'],
	 *                  ['name' => 'Sidebar2 Name', 'description' => 'Sidebar2 Description'],
	 *                  ...
	 *                ]
	 */
	function dazzle_get_sidebars() {
		$list = apply_filters(
			'dazzle_filter_list_sidebars', array(
				'sidebar_widgets'       => array(
					'name'        => esc_html__( 'Sidebar Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown on the main sidebar', 'dazzle' ),
				),
				'header_widgets'        => array(
					'name'        => esc_html__( 'Header Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown at the top of the page (in the page header area)', 'dazzle' ),
				),
				'above_page_widgets'    => array(
					'name'        => esc_html__( 'Top Page Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown below the header, but above the content and sidebar', 'dazzle' ),
				),
				'above_content_widgets' => array(
					'name'        => esc_html__( 'Above Content Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown above the content, near the sidebar', 'dazzle' ),
				),
				'below_content_widgets' => array(
					'name'        => esc_html__( 'Below Content Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown below the content, near the sidebar', 'dazzle' ),
				),
				'below_page_widgets'    => array(
					'name'        => esc_html__( 'Bottom Page Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown below the content and sidebar, but above the footer', 'dazzle' ),
				),
				'footer_widgets'        => array(
					'name'        => esc_html__( 'Footer Widgets', 'dazzle' ),
					'description' => esc_html__( 'Widgets to be shown at the bottom of the page (in the page footer area)', 'dazzle' ),
				),
			)
		);
		return $list;
	}
}


//-------------------------------------------------------
//-- Theme fonts
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_theme_fonts_links' ) ) {
	/**
	 * Return a list with links for all theme-specific fonts in the format:
	 *
	 * [
	 *   'font1-slug' => 'font1-url',
	 *   'font2-slug' => 'font2-url',
	 *   ...
	 * ]
	 * 
	 * @param bool $separate  Combine all Google fonts to the single link (if false)
	 *                        or add each font-face as separate link (if true)
	 *
	 * @return array  An array with links for all theme-specific fonts.
	 */
	function dazzle_theme_fonts_links( $separate = false ) {
		$links = array();

		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		*/
		$google_fonts_enabled = ( 'off'  !== _x( 'on', 'Google fonts: on or off', 'dazzle' ) );
		$google_fonts_api     = ( 'css2' !== _x( 'css2', 'Google fonts API: css or css2', 'dazzle' ) ? 'css' : 'css2' );
		$adobe_fonts_enabled  = ( 'off'  !== _x( 'on', 'Adobe fonts: on or off', 'dazzle' ) );
		$custom_fonts_enabled = ( 'off'  !== _x( 'on', 'Custom fonts (included in the theme): on or off', 'dazzle' ) );

		if ( ( $google_fonts_enabled || $adobe_fonts_enabled || $custom_fonts_enabled ) && ! dazzle_storage_empty( 'load_fonts' ) ) {
			$load_fonts = dazzle_storage_get( 'load_fonts' );
			if ( count( $load_fonts ) > 0 ) {
				$google_fonts_subset = dazzle_get_theme_option( 'load_fonts_subset' );
				$google_fonts = '';
				$adobe_fonts  = '';
				foreach ( $load_fonts as $font ) {
					$used = false;
					$slug = dazzle_get_load_fonts_slug( $font['name'] );
					// Custom (in the theme folder included) font
					if ( $custom_fonts_enabled && empty( $font['styles'] ) && empty( $font['link'] ) ) {
						$url = dazzle_get_file_url( "css/font-face/{$slug}/stylesheet.css" );
						if ( ! empty( $url ) ) {
							$links[ $slug ] = $url;
							$used = true;
						}
					}
					// Adobe font
					if ( $adobe_fonts_enabled && ! empty( $font['link'] ) ) {
						if ( ! in_array( $font['link'], $links ) ) {
							$links[ $slug ] = $font['link'];
						}
						$used = true;
					}
					// Google font
					if ( $google_fonts_enabled && ! $used ) {
						$link = str_replace( ' ', '+', $font['name'] )
									. ':'
									. ( empty( $font['styles'] )
										? ( 'css2' == $google_fonts_api
											? 'ital,wght@0,400;0,700;1,400;1,700'
											: '400,700,400italic,700italic'
											)
										: $font['styles']
										);
						if ( $separate ) {
							$links[ $slug ] = esc_url( "https://fonts.googleapis.com/{$google_fonts_api}?family={$link}&subset={$google_fonts_subset}&display=swap" );
						} else {
							$google_fonts .= ( $google_fonts
												? ( 'css2' == $google_fonts_api
													? '&family='
													: '|'			// Attention! Using '%7C' instead '|' damage loading second+ fonts
													)
												: ''
												)
											. $link;
						}
					}
				}
				if ( $google_fonts_enabled && ! empty( $google_fonts ) && ! $separate ) {
					$links['google_fonts'] = esc_url( "https://fonts.googleapis.com/{$google_fonts_api}?family={$google_fonts}&subset={$google_fonts_subset}&display=swap" );
				}
			}
		}
		return apply_filters( 'dazzle_filter_theme_fonts_links', $links );
	}
}

if ( ! function_exists( 'dazzle_theme_fonts_for_editor' ) ) {
	/**
	 * Return a list with links for all theme-specific fonts to use its as editor styles.
	 * 
	 * @param bool $separate  Combine all Google fonts to the single link (if false)
	 *                        or add each font-face as separate link (if true)
	 *
	 * @return array  An array with links for all theme-specific fonts
	 */
	function dazzle_theme_fonts_for_editor( $separate = false ) {
		$links = array_values( dazzle_theme_fonts_links( $separate ) );
		if ( is_array( $links ) && count( $links ) > 0 ) {
			for ( $i = 0; $i < count( $links ); $i++ ) {
				$links[ $i ] = str_replace( ',', '%2C', $links[ $i ] );
			}
		}
		return $links;
	}
}


//-------------------------------------------------------
//-- The Excerpt
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_excerpt_length' ) ) {
	/**
	 * Return an excerpt length depends of the current blog style.
	 *
	 * Hooks: add_filter( 'excerpt_length', 'dazzle_excerpt_length' );
	 *
	 * @param int $length  Current value of the length.
	 *
	 * @return int         Filtered value of the length.
	 */
	function dazzle_excerpt_length( $length ) {
		$blog_style = explode( '_', dazzle_get_theme_option( 'blog_style' ) );
		return max( 0, round( dazzle_get_theme_option( 'excerpt_length' ) / ( in_array( $blog_style[0], array( 'classic', 'masonry', 'portfolio' ) ) ? 2 : 1 ) ) );
	}
}

if ( ! function_exists( 'dazzle_excerpt_more' ) ) {
	/**
	 * Return a string '&hellip;' to append to the excerpt.
	 *
	 * Hooks: add_filter( 'excerpt_more', 'dazzle_excerpt_more' );
	 *
	 * @param string $more  A current string to append.
	 *
	 * @return string       A theme-specific string to append.
	 */
	function dazzle_excerpt_more( $more ) {
		return '&hellip;';
	}
}


//-------------------------------------------------------
//-- Comments
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_comment_form_fields' ) ) {
	/**
	 * Reorder a list with fields for the comment form - put the field 'comment' to the end of the list.
	 *
	 * Hooks: add_filter('comment_form_fields', 'dazzle_comment_form_fields');
	 *
	 * @param array $comment_fields  An array with fields for the comments form.
	 *
	 * @return array                 A reordered array with fields.
	 */
	function dazzle_comment_form_fields( $comment_fields ) {
		if ( dazzle_get_theme_setting( 'comment_after_name' ) ) {
			$keys = array_keys( $comment_fields );
			if ( 'comment' == $keys[0] ) {
				$comment_fields['comment'] = array_shift( $comment_fields );
			}
		}
		return $comment_fields;
	}
}

if ( ! function_exists( 'dazzle_comment_form_agree' ) ) {
	/**
	 * Add a checkbox with "I agree ..." to the list of fields for the comments form.
	 *
	 * Hooks: add_filter('comment_form_fields', 'dazzle_comment_form_agree', 11);
	 *
	 * @param array $comment_fields  An array with fields for the comments form.
	 *
	 * @return array                 A list with the comments form fields with a checkbox added.
	 */
	function dazzle_comment_form_agree( $comment_fields ) {
		$privacy_text = dazzle_get_privacy_text();
		if ( ! empty( $privacy_text )
			&& ( ! function_exists( 'dazzle_exists_gdpr_framework' ) || ! dazzle_exists_gdpr_framework() )
			&& ( ! function_exists( 'dazzle_exists_wp_gdpr_compliance' ) || ! dazzle_exists_wp_gdpr_compliance() )
		) {
			$comment_fields['i_agree_privacy_policy'] = dazzle_single_comments_field(
				array(
					'form_style'        => 'default',
					'field_type'        => 'checkbox',
					'field_req'         => '',
					'field_icon'        => '',
					'field_value'       => '1',
					'field_name'        => 'i_agree_privacy_policy',
					'field_title'       => $privacy_text,
				)
			);
		}
		return $comment_fields;
	}
}


//-------------------------------------------------------
//-- Thumb sizes
//-------------------------------------------------------

if ( ! function_exists( 'dazzle_theme_thumbs_sizes' ) ) {
	/**
	 * Add a retina-ready dimensions to the list with thumb sizes.
	 *
	 * Hooks: add_filter( 'image_size_names_choose', 'dazzle_theme_thumbs_sizes' );
	 *
	 * @param $sizes
	 * @return mixed
	 */
	function dazzle_theme_thumbs_sizes( $sizes ) {
		$thumb_sizes = dazzle_storage_get( 'theme_thumbs' );
		$mult        = dazzle_get_theme_option( 'retina_ready', 1 );
		foreach ( $thumb_sizes as $k => $v ) {
			$sizes[ $k ] = $v['title'];
			if ( $mult > 1 ) {
				$sizes[ $k . '-@retina' ] = $v['title'] . ' ' . esc_html__( '@2x', 'dazzle' );
			}
		}
		return $sizes;
	}
}


//-------------------------------------------------------
//-- Include theme (or child) PHP-files
//-------------------------------------------------------

// Load a theme core files
require_once DAZZLE_THEME_DIR . 'includes/utils.php';
require_once DAZZLE_THEME_DIR . 'includes/storage.php';

require_once DAZZLE_THEME_DIR . 'includes/lists.php';
require_once DAZZLE_THEME_DIR . 'includes/wp.php';

if ( is_admin() ) {
	require_once DAZZLE_THEME_DIR . 'includes/tgmpa/class-tgm-plugin-activation.php';
	require_once DAZZLE_THEME_DIR . 'includes/admin.php';
}

require_once DAZZLE_THEME_DIR . 'theme-options/theme-customizer.php';

require_once DAZZLE_THEME_DIR . 'front-page/front-page-options.php';

// Load a skins support
if ( defined( 'DAZZLE_ALLOW_SKINS' ) && DAZZLE_ALLOW_SKINS && file_exists( DAZZLE_THEME_DIR . 'skins/skins.php' ) ) {
	require_once DAZZLE_THEME_DIR . 'skins/skins.php';
}

// Load next files after the skins support loaded to allow a file substitution from the skins folder
require_once dazzle_get_file_dir( 'theme-specific/theme-tags.php' );
require_once dazzle_get_file_dir( 'theme-specific/theme-about/theme-about.php' );

// Add a free theme support
if ( DAZZLE_THEME_FREE ) {
	require_once dazzle_get_file_dir( 'theme-specific/theme-about/theme-upgrade.php' );
}

// Load an image hover effects
$dazzle_file_dir = dazzle_get_file_dir( 'theme-specific/theme-hovers/theme-hovers.php' );
if ( ! empty( $dazzle_file_dir ) ) {
	require_once dazzle_get_file_dir( 'theme-specific/theme-hovers/theme-hovers.php' );      // Substitution from skin is allowed
}

// Load a plugins support
$dazzle_required_plugins = apply_filters( 'dazzle_required_plugins', dazzle_storage_get( 'required_plugins' ) );
if ( is_array( $dazzle_required_plugins ) ) {
	foreach ( $dazzle_required_plugins as $dazzle_plugin_slug => $dazzle_plugin_data ) {
		$dazzle_plugin_slug = dazzle_esc( $dazzle_plugin_slug );
		$dazzle_plugin_path = dazzle_get_file_dir( sprintf( 'plugins/%1$s/%1$s.php', $dazzle_plugin_slug ) );
		if ( ! empty( $dazzle_plugin_path ) ) {
			require_once $dazzle_plugin_path;
		}
	}
}
