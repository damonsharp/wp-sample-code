<?php

if ( ! class_exists( 'DWSLGF_Data_Structures' ) ) :

	/**
	 * Data structure utility class to create custom post types,
	 * taxonomies, etc.
	 */
	class DWSLGF_Data_Structures {

		/**
		 * Instance of the class.
		 *
		 * @var DWSLGF_Data_Structures $instance
		 */
		private static $instance;

		/**
		 * The post types.
		 *
		 * @var array the post types.
		 */
		private $post_types = [];

		/**
		 * The taxonomies.
		 *
		 * @var array the post types.
		 */
		private $taxonomies = [];

		/**
		 * DWSLGF_Data_Structures constructor.
		 */
		private function __construct() {
			/* Don't do anything, needs to be initialized via instance() method */
		}

		/**
		 * Instance of the class.
		 *
		 * @return DWSLGF_Data_Structures $instance the class instance.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new DWSLGF_Data_Structures;
				self::$instance->setup();
			}

			return self::$instance;
		}

		/**
		 * Setup data structures.
		 */
		public function setup() {
			add_action( 'init', [ $this, 'register' ] );
		}

		/**
		 * Add the post type and its args to the array.
		 *
		 * @param string $type the post type type.
		 * @param array  $args array of post type args.
		 */
		public function add_post_type( $type, $args ) {
			$this->post_types[ $type ] = $args;
		}

		/**
		 * Add the taxonomy to the array.
		 *
		 * @param string $taxonomy the taxonomy.
		 * @param array  $args array of taxonomy args.
		 */
		public function add_taxonomy( $taxonomy, $args ) {
			$this->taxonomies[ $taxonomy ] = $args;
		}

		/**
		 * Register the post type and its taxonomies.
		 */
		public function register() {
			foreach ( $this->post_types as $type => $args ) {
				$singular = ( ! empty( $args['singular'] ) ) ? $args['singular'] : $this->titleize( $type );
				$plural   = ( ! empty( $args['plural'] ) ) ? $args['plural'] : $singular . 's';

				register_post_type( $type, array_merge( [
					'public'        => true,
					'supports'      => array_merge( $args['supports'], [ 'title' ] ),
					'menu_position' => 6,
					'labels'        => [
						'name'                  => $plural,
						'singular_name'         => $singular,
						'add_new'               => "Add New $singular",
						'add_new_item'          => "Add New $singular",
						'edit_item'             => "Edit $singular",
						'new_item'              => "New $singular",
						'all_items'             => $plural,
						'view_item'             => "View $singular",
						'search_items'          => "Search $plural",
						'not_found'             => "No $plural found",
						'not_found_in_trash'    => "No $plural found in Trash",
						'parent_item_colon'     => '',
						'menu_name'             => $plural,
						'featured_image'        => "$singular's Photo",
						'set_featured_image'    => "Add $singular's Photo",
						'remove_featured_image' => "Remove $singular's Photo",
						'use_featured_image'    => "Use as $singular's Photo",
					],
				], $args ) );
			}

			foreach ( $this->taxonomies as $taxonomy => $args ) {
				$singular = ( ! empty( $args['singular'] ) ) ? $args['singular'] : $this->titleize( $taxonomy );
				$plural   = ( ! empty( $args['plural'] ) ) ? $args['plural'] : $singular . 's';

				register_taxonomy( $taxonomy, $args['post_type'], array_merge( $args, [
					'labels' => [
						'name'                       => $plural,
						'singular_name'              => $singular,
						'search_items'               => 'Search ' . $plural,
						'popular_items'              => 'Popular ' . $plural,
						'all_items'                  => 'All ' . $plural,
						'parent_item'                => 'Parent ' . $singular,
						'parent_item_colon'          => "Parent {$singular}:",
						'edit_item'                  => 'Edit ' . $singular,
						'update_item'                => 'Update ' . $singular,
						'add_new_item'               => 'Add New ' . $singular,
						'new_item_name'              => "New {$singular} Name",
						'separate_items_with_commas' => "Separate {$plural} with commas",
						'add_or_remove_items'        => "Add or remove {$plural}",
						'choose_from_most_used'      => "Choose from the most used {$plural}",
						'not_found'                  => "No {$plural} found.",
						'menu_name'                  => $plural,
					],
				] ) );
			}
		}

		/**
		 * Title formatting.
		 *
		 * @param  string $field the field taxonomy.
		 *
		 * @return string formatted field
		 */
		public static function titleize( $field ) {
			$search  = [ '-', '_' ];
			$replace = [ ' ', ' ' ];

			return ucwords( str_replace( $search, $replace, $field ) );
		}
	}

	function DWSLGF_Data_Structures() {
		return DWSLGF_Data_Structures::instance();
	}

endif;
