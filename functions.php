<?php

    /*
    |---------------------------------------------------------------------
    | Check for MBB Theme Companion Plugin / Warn if not loaded
    |---------------------------------------------------------------------
    |
    */
    function check_theme_dependencies() {
        if ( ! defined( 'MBB_PLUGIN_DIR' ) ) {
            add_action( 'admin_notices', 'mbb_warning' );
        }
    }
    add_action( 'init', 'check_theme_dependencies' );

    function mbb_warning() {
        $html = '<div class ="update-nag"><p>%s</p></div>';
        $msg  = 'The MBB Theme Companion plugin is not activated. Please make sure it is installed and activated before continuing.';
        printf( $html, $msg );
    }


    /*
    |---------------------------------------------------------------------
    | Load up libraries, etc.
    |---------------------------------------------------------------------
    |
    */
    function mbb_setup_theme() {
        // Theme options page
        if ( defined( 'FM_VERSION' ) && defined( 'MBB_PLUGIN_DIR' ) ) {
            require_once( 'inc/class-mbb-theme-page-about.php' );
            require_once( 'inc/class-mbb-theme-page-home.php' );
            require_once( 'inc/class-mbb-theme-widget_areas.php' );
            require_once( 'inc/class-mbb-theme-contact-form.php' );
        }
    }
    add_action( 'init', 'mbb_setup_theme' );

    function mbb_filter_body_class( $classes ) {
        global $post;
        if ( ! empty( $post->post_type ) ) {
            if ( 'page' == $post->post_type ) {
                if ( is_page_template( 'page-wide.php' ) && ! in_array( 'no_sidebar', $classes ) ) {
                    $classes[] = 'no_sidebar';
                } else {
                    $classes[] = $post->post_name;
                }
            } else {
                if ( is_singular( [ 'staff', 'players' ] ) ) {
                    $classes[] = 'no_sidebar';
                }
                if ( is_home() && ( 'blog' == $classes[0] ) ) {
                    $classes[0] = 'news';
                } else {
                    $classes[] = $post->post_name;
                }
            }
        }
        return $classes;
    }
    add_filter( 'body_class', 'mbb_filter_body_class' );

    function mbb_max_srcset_image_width( $max_width, $size_array ) {
        return 800;
    }
    add_filter( 'max_srcset_image_width', 'mbb_max_srcset_image_width', 10, 2 );


    function mbb_add_image_sizes() {
        add_image_size( 'mbb-post-large', 747, 9999, false );
    }
    add_action( 'after_setup_theme', 'mbb_add_image_sizes' );


    /*
    |---------------------------------------------------------------------
    | Enqueue scripts and styles
    |---------------------------------------------------------------------
    |
    */

    /**
     * Load theme scripts and styles
     *
     * @param void
     * @return void
     */
    function mbb_enqueue_scripts_styles() {
        // Load main stylesheet
        wp_enqueue_style( 'mbb-style', get_stylesheet_uri() );
        wp_enqueue_script( 'mbb-main', get_template_directory_uri() . '/assets/js/main.min.js', [ 'jquery' ], '1.0.0', true );
    }
    add_action( 'wp_enqueue_scripts', 'mbb_enqueue_scripts_styles' );

    /*
    |---------------------------------------------------------------------
    | Helper functions
    |---------------------------------------------------------------------
    |
    */
    function mbb_get_page_template_parts() {
        $pages = get_posts(
            [
                'posts_per_page' => 100,
                'post_type' => 'page',
            ]
        );
        if ( ! empty( $pages ) ) {
            foreach ( $pages as $page ) {
                if ( is_page( $page->post_name ) ) {
                    get_template_part( "template-parts/$page->post_name" );
                }
            }
        }
    }

    /**
     * Get theme Settings
     *
     * Return settings array or value by key
     *
     * @param stging $key the array key
     * @return string or array theme setting(s)
     */
    function mbb_get_theme_settings( $key = '' ) {
        $mbb_theme_settings = get_option( 'mbb_theme_settings' );
        if ( ! empty( $mbb_theme_settings ) ) {
            if ( ! empty( $key ) && ! empty( $mbb_theme_settings[ $key ] ) ) {
                return $mbb_theme_settings[ $key ];
            }
            return $mbb_theme_settings;
        }
    }

    /**
     * Display site footer
     *
     * Show sidebar within footer.php if the page should
     * contain a sidebar basad on classes in filtered
     * body class
     *
     * @uses mbb_filter_body_class()
     * @param void
     * @return void
     */
    function mbb_get_footer() {
        $search = 'no_sidebar';
        $classes = get_body_class();
        if ( ! empty( $classes ) && is_array( $classes) && ! in_array( $search, $classes ) ) {
            get_sidebar();
        }
    }

    function mbb_get_template_part( $template_part ) {
        if ( ! empty( $template_part ) ) {
            require_once( locate_template( $template_part ) );
        }
    }
