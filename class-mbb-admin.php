<?php

if ( ! class_exists( 'MBB_Admin' ) ) :

	/**
	 * Class for admin item customizations
	 */
	class MBB_Admin {

		/**
		 * Instance of the class.
		 *
		 * @var MBB_Admin instance of the class.
		 */
		private static $instance;

		/**
		 * Title for content displayed after default
		 * WP title input.
		 *
		 * @var string content after post title.
		 */
		private $content_after_title = '';

		/**
		 * Placeholder text for the default WP title input
		 *
		 * @var string customized text for the post title placeholder.
		 */
		private $enter_title_here_text;

		/**
		 * MBB_Admin constructor.
		 */
		private function __construct() {
			/* Don't do anything, needs to be initialized via instance() method */
		}

		/**
		 * Create one instance of the class
		 *
		 * @return MBB_Admin instance of the class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new MBB_Admin;
				self::$instance->setup();
			}

			return self::$instance;
		}

		/**
		 * Setup actions, filters, enqueue scripts/styles, etc.
		 */
		public function setup() {
			// Add content under title.
			add_action( 'edit_form_after_title', [ $this, 'add_content_after_title' ] );

			// Load admin scripts/styles.
			add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_scripts_styles' ] );

			// Load front-end scripts/styles.
			add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts_styles' ] );

			// Remove theme supports.
			add_action( 'init', [ $this, 'theme_supports' ] );

			// Change 'title here' text.
			add_filter( 'enter_title_here', [ $this, 'change_title_here_text' ], 11, 2 );

			// Register navigation menus & add theme supports. Already loaded on 'after_setup_theme' here.
			$this->register_nav_menus();
			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}

			// Register/Unregister widgets.
			add_action( 'widgets_init', [ $this, 'register_widgets' ] );
			add_action( 'widgets_init', [ $this, 'unregister_widgets' ] );

			// Add feeds for custom post types.
			add_filter( 'wp_head', [ $this, 'add_custom_post_type_rss' ] );
		}

		/**
		 * Set the content to be displayed on the edit screen after the title.
		 *
		 * @param mixed $content the content to display.
		 */
		public function set_content_after_title( $content ) {
			$this->content_after_title = $content;
		}

		/**
		 * Callback for adding content after the edit post screen title.
		 */
		public function add_content_after_title() {
			echo wp_kses_post( $this->content_after_title );
		}

		/**
		 * Set the 'enter title text' here.
		 *
		 * @param string $content the text to display.
		 */
		public function set_enter_title_here_text( $content ) {
			$this->enter_title_here_text = $content;
		}

		/**
		 * Change the title here text based on the post type.
		 *
		 * @param  string  $title the title copy.
		 * @param  WP_Post $post  the post object.
		 *
		 * @return string $title
		 */
		public function change_title_here_text( $title, $post ) {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( ! empty( $screen->post_type ) && post_type_exists( $screen->post_type ) && $post->post_type === $screen->post_type ) {
					$title = $this->enter_title_here_text;
				} else {
					$title = 'Enter the title here';
				}
			}

			return $title;
		}

		/**
		 * Only administrators can edit categories.
		 */
		public static function remove_manage_categories_capability() {
			$editor = get_role( 'editor' );
			$editor->remove_cap( 'manage_categories' );
		}

		/**
		 * Enqueue admin scripts and styles.
		 */
		public function load_admin_scripts_styles() {
			wp_enqueue_style( 'mbb-admin-styles', MBB_PLUGIN_DIR_URL . 'assets/css/mbb-admin.css', [], '1.0.0', 'screen' );
			wp_enqueue_script( 'mbb-admin-script', MBB_PLUGIN_DIR_URL . 'assets/js/mbb_admin.min.js', false, true );
		}

		/**
		 * Enqueue frontend scripts and styles.
		 */
		public function load_scripts_styles() {
			wp_enqueue_script( 'mbb-notifications', MBB_PLUGIN_DIR_URL . 'assets/js/mbb_notifications.min.js', [ 'jquery' ], '1.0.0', false );
		}

		/**
		 * Add and remove theme supports.
		 */
		public function theme_supports() {
			foreach ( MBB_Helpers()->get_remove_theme_support_items() as $item ) {
				if ( current_theme_supports( $item ) ) {
					remove_theme_support( $item );
				}
			}
			foreach ( MBB_Helpers()->get_add_theme_support_items() as $item ) {
				if ( ! current_theme_supports( $item ) ) {
					add_theme_support( $item );
				}
			}
		}

		/**
		 * Register navigation menus.
		 */
		public function register_nav_menus() {
			foreach ( MBB_Helpers()->get_nav_menus() as $location => $desc ) {
				register_nav_menu( $location, $desc );
			}
		}

		/**
		 * Register widget areas.
		 */
		public function register_widgets() {
			foreach ( MBB_Helpers()->get_registered_widgets() as $widget ) {
				register_widget( $widget );
			}
		}

		/**
		 * Unregister widgets.
		 */
		public function unregister_widgets() {
			foreach ( MBB_Helpers()->get_unregistered_widgets() as $widget ) {
				unregister_widget( $widget );
			}
		}

		/**
		 * Add custom post type rss.
		 */
		public function add_custom_post_type_rss() {
			$post_types = array( 'events' );
			foreach ( $post_types as $post_type ) {
				$feed = get_post_type_archive_feed_link( $post_type );
				if ( '' === $feed || ! is_string( $feed ) ) {
					$feed = get_bloginfo( 'rss2_url' ) . "?post_type=$post_type";
				}
				echo wp_kses_post( sprintf( '<link rel="%1$s" type="%2$s" href="%3$s" />', 'alternate', 'application/rss+xml', $feed ) );
			}
		}
	}

	function MBB_Admin() {
		return MBB_Admin::instance();
	}

	register_activation_hook( __FILE__, [ 'MBB_Admin', 'remove_manage_categories_capability' ] );

endif;
