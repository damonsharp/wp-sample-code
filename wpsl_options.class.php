<?php namespace SWS_WPSL;

	defined( 'ABSPATH' ) OR exit;

	if ( ! class_exists( 'Options' ) ) {

		class Options extends WPSL {

			/**
			 * Hold html for page view
			 */
			public $view_src;


			/**
			 * Constructor
			 *
			 * Set $opts array and actions for creating options pages
			 *
			 * @since 1.0
			 * @param void
			 * @return void
			 */
			public function __construct() {

				parent::__construct();
				// Create plugin options pages based on $this->opts
				// ** NOTE: Keep priority at 9 or lower *******************************
				add_action( 'admin_menu', array($this, 'initialize_option_pages'), 9 );

				// Add options to option pages above
				add_action( 'admin_init', array($this, 'register_options') );

			}


			/**
			 * Using the config array, setup the main options page and any subpages
			 *
			 * Additionally this will call the method to update the main plugin page sidebar label
			 * to "Dashboard"
			 *
			 * @since 1.0
			 * @param void
			 * @return html page content
			 */
			public function initialize_option_pages() {

				foreach ( $this->opts['option_pages'] as $page ) {
					if ( $page['type'] == 'menu' ) {
						add_menu_page($page['title'], $page['menu_title'], $page['capability'], $page['menu_slug'], array($this, $page['function']), $page['icon_url'], $page['position']);
					} else {
						add_submenu_page($page['parent'], $page['title'], $page['menu_title'], $page['capability'], $page['menu_slug'], array($this, $page['function']));
					}
				}
				$this->modify_menus();

			}


			/**
			 * Callback from initialize_option_pages() above to register option options
			 *
			 * @since 1.0
			 * @param void
			 * @return html page content
			 */
			public function register_options() {
				// Register theme options page, create sections and options fields
				register_setting('wpsl_core', 'wpsl_core', array('SWS_Validation', 'process_options') );
				
				// Plugin options from config file
				foreach ( $this->opts['option_pages'] as $page ) {
					if ( sws_wpsl_page_is($page['menu_slug']) ) {
						$this->view_src = $page['view_src'];
						add_settings_section( $page['menu_slug'], $page['title'], array($this, 'option_page_content'), $page['menu_slug'] );
					}
				}
			}


			/**
			 * Callback from register_options() above to add in main options page shell
			 *
			 * This utilizes a custom helper function from helpers/sws_wpsl_helpers.php
			 *
			 * @since 1.0
			 * @param void
			 * @return html page content
			 */
			public function option_page_template() {

				sws_get_plugin_part( SWS_WPSL_OPTIONS_PAGES, 'sws_options_page_template' );

			}


			/**
			 * Callback from add_settings_section above. This function includes a file with the
			 * same filename as the $page['id'] passed in.
			 *
			 * This utilizes a custom helper function from helpers/sws_wpsl_helpers.php
			 *
			 * @since 1.0
			 * @param void
			 * @return html page content
			 */
			public function option_page_content( $page ) {

				sws_get_plugin_part( $this->view_src, $page['id'] );

			}


			/**
			 * Modification of the main plugin pages label/title in the admin nav menu
			 *
			 * @since 1.0
			 * @param void
			 * @return string admin menu label
			 */
			public function modify_menus() {

				global $submenu;
				if ( isset( $submenu['sws_wpsl_dashboard'] ) ) {

					$submenu['sws_wpsl_dashboard'][0][0] = __( 'Dashboard', 'sws_wpsl' );

				}

			}

		}

	}