<?php

	class ThemeFunctions {

		/**
		 * [$opts description]
		 * @var [type]
		 */
		public $opts;

		/**
		 * [$screen_id description]
		 * @var [type]
		 */
		public $screen_id;

		/**
		 * Construct
		 * 
		 * Set options, initialize theme and setup WooCommerce hooks
		 * 
		 * @param void
		 * @return void
		 */
		public function __construct() {

			$this->opts = require_once( 'theme_opts.php' );

			$this->woocommerce_hooks();

			$this->theme_init();

		}

		/**
		 * WooCommerce Hooks
		 * 
		 * Customize WooCommerce implementation through actions and filters
		 * 
		 * @param void
		 * @return void
		 */
		public function woocommerce_hooks() {

			/************************************************************************************
			** Single Product Pages *************************************************************
			************************************************************************************/

			// Modify WooCommerce wrappers
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );

			// Add necessary bootstrap structure (see partials/woocommerce for template parts)
			add_action( 'woocommerce_before_single_product', array( $this, 'modify_woocommerce_before_single_product' ) );
			add_action( 'woocommerce_after_single_product', array( $this, 'modify_woocommerce_after_single_product' ) );

			// Skip cart page
			add_filter('add_to_cart_redirect', array( $this, 'custom_add_to_cart_redirect' ) );

			// Remove cart thumbnails
			add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );

			// Remove product tabs
			add_filter( 'woocommerce_product_tabs', function() { return array(); } );

			// Add custom links for NDA and idea submission to new order complete email
			add_action( 'woocommerce_email_before_order_table', array( $this, 'new_order_before_email_table' ) );

			remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);

			// Remove product page items, both before and after single product summary
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

			// WooCommerce sidebar
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
			
			// Change order of single product page output
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );

			remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
			add_action( 'woocommerce_single_product_summary', 'wc_print_notices', 10 );

			// Only allow purchase of one item
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woo_add_only_one_to_cart' ) );


			/************************************************************************************
			** Checkout Page ********************************************************************
			************************************************************************************/
			add_filter( 'woocommerce_order_button_html', array( $this, 'modify_woocommerce_checkout_btn' ) );

			// Add bootstap classes for form field groups
			add_filter( 'woocommerce_checkout_fields' , array( $this, 'override_checkout_form_field_groups' ) );

			/************************************************************************************
			** Shop Page ************************************************************************
			************************************************************************************/
			add_filter( 'woocommerce_return_to_shop_redirect', array( $this, 'change_return_to_shop_url' ) );

		}

		/**
		 * Theme Init
		 * 
		 * Initialize theme specific hooks - non-WooCommerce specific
		 * 
		 * @param void
		 * @return void
		 */
		public function theme_init() {

			// WP Editor access
			$this->restrict_wp_editor_access();

			// Setup actions and filters
			add_action( 'init', array( $this, 'initialize_theme' ) );
			add_action( 'wp_head', array( $this, 'custom_image_bg_css' ) );

			//add_action( 'wp', array( $this, 'get_current_screen_info' ) );
			add_action( 'widgets_init', array( $this, 'add_widget_areas' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ), 50 );

			// Filter the title for to add a "<br>" between first and last name
			add_filter( 'the_title', array( $this, 'break_post_title_copy' ) );

			// Remove image dimensions
			add_filter( 'post_thumbnail_html', array( $this, 'remove_image_dimensions' ) );
			add_filter( 'image_send_to_editor', array( $this, 'remove_image_dimensions' ) );
			add_filter( 'the_content', array( $this, 'remove_image_dimensions' ) );

			add_filter( 'wp_title', array( $this, 'theme_site_title' ), 10, 2 );
			add_filter( 'body_class', array( $this, 'additional_body_classes' ) );
			add_filter( 'post_class', array( $this, 'additional_post_classes' ) );
			
			// Add logo to admin login page
			add_action( 'login_enqueue_scripts', array( $this, 'custom_login_logo' ) );
			remove_action( 'the_content', 'wpautop' );

		}

		/**
		 * Wrap WooCommerce templates in Bootstrap container
		 * 
		 * @return string html containing element opening tag
		 */
		public function modify_woocommerce_wrapper_start() {

			echo '<div class="container">';

		}

		/**
		 * Close wrap of WooCommerce templates in Bootstrap container
		 * 
		 * @return string html ending containing element tag
		 */
		public function modify_woocommerce_wrapper_end() {

			echo '</div><!-- end .container -->';

		}

		/**
		 * Add additional markup at the top of single product page
		 * 
		 * @param  void
		 * @return html markup
		 */
		public function modify_woocommerce_before_single_product() {

			get_template_part( 'partials/woocommerce/single-product', 'top' );

		}

		/**
		 * Add additional markup at the bottom of single product page
		 * 
		 * @return html markup
		 */
		public function modify_woocommerce_after_single_product() {

			get_template_part( 'partials/woocommerce/single-product', 'btm' );

		}

		/**
		 * Run items on init
		 * 
		 * @return void
		 */
		public function initialize_theme() {

			require_once( get_template_directory() . '/inc/helpers.php' );

			// Add WooCommerce theme support
			add_theme_support( 'woocommerce' );

			// Add custom image sizes
			add_image_size( 'theme-page-banner', 1700, 600, true );
			add_image_size( 'theme-carousel', 1170, 490, true );

		}

		/**
		 * Create widget areas based on options array
		 * 
		 * @return false;
		 */
		public function add_widget_areas() {

			foreach ( $this->opts['sidebars'] as $args ) {

				register_sidebar( $args );
				
			}

		}

		/**
		 * Add scripts and styles
		 * 
		 * @return void
		 */
		public function enqueue_scripts_styles() {

			wp_enqueue_style( 'main', get_stylesheet_uri(), array(), '1.0' );
			// wp_enqueue_script( 'jquery', false, false, false, true );
			wp_enqueue_script( 'bootstrapjs', get_stylesheet_directory_uri() . '/assets/js/bootstrap.min.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'mainjs', get_stylesheet_directory_uri() . '/assets/js/main.min.js', array( 'jquery', 'bootstrapjs' ), '1.0', true );

		}

		/**
		 * Break post title into 2 lines for certain post types
		 * on certain pages
		 * 
		 * @param  string $title title of the post
		 * @return string filtered title of the post
		 */
		public function break_post_title_copy( $title ) {

			global $post;

			if ( ! is_null( $post ) ) {
				$post_type = get_post_type($post->ID);
				// If not services page, etc.
				if ( ! is_page( 953 ) && ( 'team_members' == $post_type || 'services' == $post_type ) && ! is_single() ) {
					$title = str_replace( ' ', '<br>', $title );
				}
			}
			return $title;

		}

		/**
		 * Filter all images to remove width and height attributes for RWD
		 * 
		 * @param  string $html image html
		 * @return html filtered image html
		 */
		public function remove_image_dimensions( $html ) {

			return preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );

		}

		/**
		 * Filter the site title
		 * 
		 * @param  string $title site title
		 * @param  string $sep title separator
		 * @return string filtered site title
		 */
		public function theme_site_title( $title, $sep ) {

			global $paged, $page;

			if ( is_feed() ) {
			    return $title;
			}

			// Add the site name.
			$title .= get_bloginfo( 'name' );

			// Add the site description for the home/front page.
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) ) {
			    $title = "$title $sep $site_description";
			}

			// Add a page number if necessary.
			if ( $paged >= 2 || $page >= 2 ) {
			    $title = "$title $sep " . sprintf( __( 'Page %s', 'theme' ), max( $paged, $page ) );
			}

			return $title;

		}

		/**
		 * Add custom page background image styling to head
		 * 
		 * This applies to the page banners
		 * 
		 * @param  void
		 * @return string css markup
		 */
		public function custom_image_bg_css() {

			global $post;
			$output = '';

			if ( ! is_admin() ) {

				$image = get_field( 'page_background_banner_image', $post->ID );
				$bg_img_obj = $image ? $image : get_field( 'page_background_banner_image', get_option( 'page_for_posts' ) );

				if ( $bg_img_obj ) {

					$url = $bg_img_obj['url'];
					$output = "<style>\n";
					$output .= "\t.page-intro .primary {\n";
					$output .= "\t\t" . 'background-image: url(%s);' . "\n";
					$output .= "\t\t" . 'background-size: %s %s;' . "\n";
					$output .= "\t\t" . 'background-position: %s %s;' . "\n";
					$output .= "\t}\n";
					$output .= "</style>";

					$output = sprintf( $output, $url, '100%', 'auto', 'center', 'center' );

				}
			
			}
			echo $output;

		}

		/**
		 * Add addition body classes based on the current post type
		 * 
		 * @param  array $classes array of body classes
		 * @return array filtered array of body classes or original array
		 */
		public function additional_body_classes( $classes ) {

			global $post;
			if ( $post && ! in_array( $post->post_name, $classes ) ) {
				$classes[] = $post->post_name;
			}
			return $classes;

		}

		/**
		 * Filter the post class array for specific post types
		 * 	
		 * @param  array $classes array of CSS classes
		 * @return array filtered array of CSS classes or original array
		 */
		public function additional_post_classes( $classes ) {

			global $post;

			if ( ! in_array( $post->post_name, $classes ) ) {

				switch ( $post->post_type ) {

					case 'product':
						array_push( $classes, 'row' );
						break;
					
					default:
						# code...
						break;
				}
			}
			return $classes;

		}

		/**
		 * Add html before the table on the WooCommerce new order email
		 * 
		 * @return html $output
		 */
		public function new_order_before_email_table() {

			get_template_part( 'partials/woocommerce/email-purchase-confirm-next', 'steps' );

		}

		/**
		 * Filter WooCommerce cart redirect
		 * @return string url of new redirect link
		 */
		public function custom_add_to_cart_redirect() {

		     return get_permalink( get_option( 'woocommerce_checkout_page_id' ) );

		}

		/**
		 * Remove the WooCommerce "Free" copy for products == $0
		 * 
		 * @param  string $notice free notice copy
		 * @return string empty string
		 */
		public function hide_free_price_notice( $notice ) {
		
			return '';

		}

		/**
		 * Replace the WooCommerce checkout button
		 * 
		 * Use bootstrap and custom classes
		 * 
		 * @param string $btn_html button html
		 * @return string filtered button html
		 */
		public function modify_woocommerce_checkout_btn( $btn_html ) {

			return '<input type="submit" class="btn btn-warning btn-cta" name="woocommerce_checkout_place_order" id="place_order" value="Purchase" data-value="Purchase" />';

		}

		/**
		 * Filter WooCommerce checkout fields to add bootstrap classes
		 * @param  array $fields array of fields
		 * @return array filtered array of fields
		 */
		public function override_checkout_form_field_groups( $fields ) {
			foreach ( $fields as $fieldset => $field ) {

				foreach ( $field as $attribute => $value ) {

					$fields[$fieldset][$attribute]['class'][] = 'form-group';

				}
		
			}
			return $fields;
		}

		/**
		 * Move the single product page notices to a different place
		 * on the page by modifying the action priority
		 * 
		 * @return void
		 */
		public function move_single_product_page_notices() {

			do_action( 'wp_print_notices' );

		}

 		/**
 		 * Allow one item in WooCommerce cart
 		 * 
 		 * User can only choose 1 of 3 packages to purchase so the
 		 * cart is emptied and the newly added item replaces the
 		 * current item
 		 * 
 		 * @param object $cart_item_data card item object
 		 * @return object $cart_item_data newly added cart item
 		 */
		public function woo_add_only_one_to_cart( $cart_item_data ) {

			global $woocommerce;
			$woocommerce->cart->empty_cart();

			return $cart_item_data;
			
		}

		/**
		 * Change the WooCommerce return to shop url
		 * 
		 * @param  string $url current redirect url
		 * @return string filtered url
		 */
		public function change_return_to_shop_url( $url ) {

			return get_home_url( null, 'hire-us', 'https' );

		}

		/**
		 * Replace the standard WordPress admin login logo
		 * 
		 * @return css style markup
		 */
		public function custom_login_logo() { ?>

			<style type="text/css">
				body.login div#login h1 a {
					background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/img/theme_logo_login.png);
				}
				.login h1 a {
					-webkit-background-size: auto;
					background-size: auto;
					width: auto;
					margin-bottom: 0;
				}		
			</style>
		<?php }

		/**
		 * Remove WordPress automatic paragraph tag generation
		 * 
		 * Only apply if on the "System" page ( id = 951 )
		 * 
		 * @param  html blog content
		 * @return html filtered blog content
		 */
		public function remove_specific_wpautop( $content ) {

			if ( is_page( '951' ) ) {

				remove_filter( 'the_content', 'wpautop' );

			}
			return $content;

		}


		/**
		 * Restrict access to WordPress Editor (Appearance > Editor in admin)
		 * for usernam "somuser"
		 * 
		 * @param void
		 * @return void
		 */
		public function restrict_wp_editor_access() {

			$user = wp_get_current_user();
			if ( 'someuser' != $user->user_login && ! defined( 'DISALLOW_FILE_EDIT' ) ) {

				define( 'DISALLOW_FILE_EDIT', true );

			}

		}


	}
	new ThemeFunctions();