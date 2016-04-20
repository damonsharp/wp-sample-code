<?php

if ( ! class_exists( 'MBB_Players' ) ) :

	/**
	 * Monarch Baseball Players
	 *
	 * Create custom post type, taxonomies, and metaboxes for players
	 *
	 * @uses  WordPress Fieldmanager plugin (http://fieldmanager.org)
	 */
	class MBB_Players {

		private $post_type = 'players';

		/**
		 * Replacement values for custom featured
		 * image html
		 *
		 * @var array
		 */
		private $replace = [
			"Upload player's photo",
			"Remove player's photo"
		];

		private $grades = [
			'freshman'	=> 'Freshman',
			'sophomore'	=> 'Sophomore',
			'junior'	=> 'Junior',
			'senior'	=> 'Senior',
			'grad'		=> 'Graduate/Previous Player',
		];

		private $bats = [
			'L' => 'Left',
			'R' => 'Right',
			'B' => "Both",
		];

		private $throws = [
			'L' => 'Left',
			'R' => 'Right',
		];

		/**
		 * Baseball positions
		 * @var array
		 */
		private $positions = [
			'RHP'	=> 'Right Handed Pitcher (RHP)',
			'LHP'	=> 'Left Handed Pitcher (LHP)',
			'C'		=> 'Catcher (C)',
			'1B'	=> 'First Base (1B)',
			'2B'	=> 'Second Base (2B)',
			'3B'	=> 'Third Base (3B)',
			'SS'	=> 'Shortstop (SS)',
			'LF'	=> 'Left Field (LF)',
			'CF'	=> 'Center Field (CF)',
			'RF'	=> 'Right Field (RF)',
			'IF'	=> 'Infield (IF)',
			'OF'	=> 'Outfield (OF)',
			'DH'	=> 'Designated Hitter (DH)',
		];

		/**
		 * Instance of the class
		 *
		 * @var object
		 */
		private static $instance;

		private function __construct() {
			/* Don't do anything, needs to be initialized via instance() method */
		}

		/**
		 * Create one instance of the class
		 *
		 * @return object instance of the class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new MBB_Players;
				self::$instance->setup();
			}
			return self::$instance;
		}

		/**
		 * Fire off custom post type creation, etc. here
		 *
		 * @return void
		 */
		public function setup() {
			// Register post type with any override args
			MBB_Data_Structures()->add_post_type( $this->post_type, [
				'singular'	 => 'Player',
				'supports'	 => [ 'title', 'thumbnail', 'page-attributes' ],
				'public'     => true,
			] );
			add_action( 'fm_post_players', [ $this, 'init' ] );
		}

		/**
		 * Fire off the meta box, etc. creation here
		 *
		 * @return void
		 */
		public function init() {
			// Player's Details
			$fm = new Fieldmanager_Group( [
				'name'		=> 'player_details',
				'tabbed'	=> 'vertical',
				'children'	=> [
					// Bio
					'bio' => new Fieldmanager_Group( [
						'label'		=> 'Bio',
						'children'	=> [
							'bio' => new Fieldmanager_RichTextArea( [
								'buttons_1'       => [ 'bold', 'italic', 'bullist', 'numlist', 'link', 'unlink' ],
								'buttons_2'       => [],
								'editor_settings' => [
									'quicktags'		=> false,
									'media_buttons'	=> false,
									'editor_height' => '240px',
						        ],
						        'description' => 'Enter information about player, including years on team, personal goals, etc.',
							] ),
						],
					] ),
					// Details
					'details' => new Fieldmanager_Group( [
						'label' => 'Details',
						'children' => [
							// Player's Grade
							'grade' => new Fieldmanager_Radios( "Current Grade/Status:", [
								'options'       => $this->grades,
								'default_value' => 'freshman',
							] ),
							'grad_year' => new Fieldmanager_Textfield( "Year Graduated", [
								'display_if' => [
									'src'	=> 'grade',
									'value'	=> 'grad',
								],
								'attributes' => [ 'size' => 6, 'maxlength' => 4 ],
							] ),
							// Player's Position(s)
							'positions' => new Fieldmanager_Checkboxes( "Position(s):", [
								'options' => $this->positions,
							] ),
							// Player's batting direction
							'bats' => new Fieldmanager_Radios( "Bats:", [
								'options'		=> $this->bats,
								'default_value'	=> 'R',
							] ),
							// Player's throwing hand
							'throws' => new Fieldmanager_Radios( "Throws:", [
								'options'		=> $this->throws,
								'default_value'	=> 'R',
							] ),
							// Player's height
							'height_feet'	=> new Fieldmanager_Textfield( "Height:", [
								'attributes' => [ 'size' => 3, 'maxlength' => 1 ],
							] ),
							'height_inches'	=> new Fieldmanager_Textfield( "", [
								'attributes' => [ 'size' => 4, 'maxlength' => 2 ],
							] ),
							// Player's weight
							'weigth' => new Fieldmanager_Textfield( "Weight (lbs):", [
								'attributes' => [ 'size' => 5, 'maxlength' => 3 ],
							] ),
							// Player's jersey numbers
							'jersey_spring' => new Fieldmanager_Textfield( "Spring Jersey Number:", [
								'attributes' => [ 'size' => 4, 'maxlength' => 2 ],
							] ),
							'jersey_summer' => new Fieldmanager_Textfield( "Summer Jersey Number:", [
								'description'	=> 'Leave Summer Jersey Number blank if not playing on a summer team or if both numbers are the same',
								'attributes'	=> [ 'size' => 4, 'maxlength' => 2 ],
							] ),
						],
					] ),
					// Contact Info
					'contact' => new Fieldmanager_Group( [
						'label'		=> 'Contact Info',
						'children'	=> [
							'mobile_number'    => new Fieldmanager_Textfield( 'Mobile Number' ),
							'email_address'    => new Fieldmanager_Textfield( 'Email Address' ),
							'twitter_username' => new Fieldmanager_Textfield( 'Twitter Username' ),
						],
					] ),
					// Videos, Highlights, etc.
					'recruiting' => new Fieldmanager_Group( [
						'label'		=> 'Recruiting Info',
						'children'	=> [
							'pbr_profile_url'	=> new Fieldmanager_Link( 'PBR Profile URL' ),
							'youtube'			=> new Fieldmanager_Group( [
								'label'		=> 'YouTube Video',
								'limit'		=> '5',
								'children'	=> [
									'title' => new Fieldmanager_Textfield( 'Title/Description (ex. Pitching - mound view)' ),
									'url'   => new Fieldmanager_Link( 'Video URL (ex. https://youtu.be/xxxxxxxxxxx)' ),
								],
								'add_more_label' => 'Add Another YouTube Link',
							] ),
							'personal_website' => new Fieldmanager_Link( 'Personal Website URL' ),
						],
					] ),
					// Miscellaneous
					'misc' => new Fieldmanager_Group( [
						'label'		=> 'Miscellaneous',
						'children'	=> [
							// Player's favorite song
							'fav_song'       => new Fieldmanager_Textfield( "Favorite song:" ),
							// Player's favorite MLB team
							'fav_mlb_team'   => new Fieldmanager_Textfield( "Favorite MLB team:" ),
							// Player's favorite MLB player
							'fav_mlb_player' => new Fieldmanager_Textfield( "Favorite MLB player:" ),
							// Player's favorite inspirational quote
							'fav_quote'	     => new Fieldmanager_Textarea( "Favorite inspirational quote:", [
								'attributes'  => [
									'style' => 'width: 100%; height: 120px',
								],
								'field_class' => 'widefat',
							] ),
						],
					] ),
				],
			] );
			$fm->add_meta_box( 'Player Details', $this->post_type );

			MBB_Admin()->set_enter_title_here_text( "Enter the player's first and last name here" );
		}
	}

	MBB_Players::instance();

endif;
