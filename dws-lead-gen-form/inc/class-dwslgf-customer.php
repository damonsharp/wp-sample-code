<?php

if ( ! class_exists( 'DWSLGF_Customer' ) ) :

	/**
	 * Customers
	 *
	 * Create custom post type, taxonomies, and metaboxes for customers
	 *
	 * @uses  WordPress Fieldmanager plugin (http://fieldmanager.org)
	 */
	class DWSLGF_Customer {

		/**
		 * The post type.
		 *
		 * @var string the post type.
		 */
		private $post_type = DWSLGF_PREFIX . '_customer';

		/**
		 * Instance of the class.
		 *
		 * @var DWSLGF_Customer $instance
		 */
		private static $instance;

		/**
		 * DWSLGF_Customer constructor.
		 */
		private function __construct() {
			/* Don't do anything, needs to be initialized via instance() method */
		}

		/**
		 * Create one instance of the class
		 *
		 * @return DWSLGF_Customer instance of the class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new DWSLGF_Customer;
				self::$instance->setup();
			}
			return self::$instance;
		}

		/**
		 * Fire off custom post type creation, etc. here
		 */
		public function setup() {
			DWSLGF_Data_Structures()->add_post_type( $this->post_type, [
				'singular'		=> 'Customer',
				'supports'		=> [ 'title' ],
				'public'		=> false,
				'show_ui'		=> true,
				'show_in_menu'	=> true,
			] );
			add_action( "fm_post_{$this->post_type}", [ $this, 'init' ] );
		}

		/**
		 * Fire off the meta box, etc. creation here
		 */
		public function init() {
			$fm = new Fieldmanager_TextField( [
				'name' => DWSLGF_PREFIX . '_phone',
			] );
			$fm->add_meta_box( 'Customer Phone Number', [ $this->post_type ] );

			$fm = new Fieldmanager_TextField( [
				'name' => DWSLGF_PREFIX . '_email',
			] );
			$fm->add_meta_box( 'Customer Email Address', [ $this->post_type ] );

			$fm = new Fieldmanager_TextField( [
				'name' => DWSLGF_PREFIX . '_budget',
			] );
			$fm->add_meta_box( 'Customer Desired Budget', [ $this->post_type ] );

			$fm = new Fieldmanager_TextArea( [
				'name' => DWSLGF_PREFIX . '_message',
			] );
			$fm->add_meta_box( 'Customer Message', [ $this->post_type ] );

			$fm = new Fieldmanager_TextField( [
				'name' => DWSLGF_PREFIX . '_submission_datetime',
			] );
			$fm->add_meta_box( 'Customer Posted Date/Time', $this->post_type );
		}
	}

	DWSLGF_Customer::instance();

endif;
