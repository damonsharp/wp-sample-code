<?php

if ( ! class_exists( 'MBB_FAQs' ) ) :

	/**
	 * Monarch Baseball FAQs
	 *
	 * Create custom post type, taxonomies, and metaboxes for faqs
	 *
	 * @uses  WordPress Fieldmanager plugin (http://fieldmanager.org)
	 */
	class MBB_FAQs {

		/**
		 * The post type.
		 *
		 * @var string the post type.
		 */
		private $post_type = 'faqs';

		/**
		 * Instance of the class.
		 *
		 * @var MBB_FAQs $instance
		 */
		private static $instance;

		/**
		 * MBB_FAQs constructor.
		 */
		private function __construct() {
			/* Don't do anything, needs to be initialized via instance() method */
		}

		/**
		 * Create one instance of the class
		 *
		 * @return MBB_FAQs instance of the class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new MBB_FAQs;
				self::$instance->setup();
			}

			return self::$instance;
		}

		/**
		 * Fire off custom post type creation, etc. here
		 */
		public function setup() {
			MBB_Data_Structures()->add_post_type( $this->post_type, [
				'singular'     => 'FAQ',
				'supports'     => [ 'title' ],
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => true,
			] );
			add_action( 'fm_post_faqs', [ $this, 'init' ] );

			MBB_Data_Structures()->add_taxonomy( 'sections', [
				'post_type'         => $this->post_type,
				'singular'          => 'Section',
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_nav_menus' => false,
				'show_tagcloud'     => false,
				'show_admin_column' => true,
				'description'       => 'FAQ sections allow for grouping specific questions/answers for display.',
			] );
		}

		/**
		 * Fire off the meta box, etc. creation here
		 */
		public function init() {
			$fm = new Fieldmanager_RichTextArea( [
				'name'            => 'faq_answer',
				'buttons_1'       => [ 'bold', 'italic', 'bullist', 'numlist', 'link', 'unlink' ],
				'buttons_2'       => [],
				'editor_settings' => [
					'quicktags'     => false,
					'media_buttons' => false,
					'editor_height' => '500px',
					'required'      => true,
				],
			] );
			$fm->add_meta_box( 'FAQ Answer', $this->post_type );

			MBB_Admin()->set_enter_title_here_text( 'Enter the frequently asked question here' );
		}
	}

	MBB_FAQs::instance();

endif;
