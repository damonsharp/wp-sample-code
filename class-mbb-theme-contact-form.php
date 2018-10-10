<?php

if ( ! class_exists( 'MBB_Contact_Form' ) ) :

	/**
	 * Monarch Baseball contact form processing
	 *
	 * Contact form processing
	 */
	class MBB_Contact_Form {

		/**
		 * Instance of the class
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Construct
		 *
		 * Nothing to see here
		 */
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
				self::$instance = new MBB_Contact_Form;
				self::$instance->setup();
			}

			return self::$instance;
		}

		/**
		 * Fire off custom post type creation, script/style enqueus, etc. here
		 *
		 * @return void
		 */
		public function setup() {
			// Get contact form settings from theme settings
			$this->contact_form_settings = MBB_Query()->get_setting( 'contact_form' );

			// Enqueue scripts
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_styles' ] );

			// Form processing setup
			add_action( 'wp_ajax_process_contact_form', [ $this, 'process_contact_form' ] );
			add_action( 'wp_ajax_nopriv_process_contact_form', [ $this, 'process_contact_form' ] );
		}

		/**
		 * Enqueue frontend scripts and styles
		 *
		 * @return void
		 */
		public function enqueue_scripts_styles() {
			if ( is_page( 'contact' ) ) {
				wp_enqueue_script( 'mbb-contact', get_template_directory_uri() . '/assets/js/contact.min.js', [ 'jquery' ], '1.0.0', true );
				wp_localize_script( 'mbb-contact', 'mbbContact', [
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'loadingIcon' => esc_url( site_url( 'wp-content/themes/monarchbaseball/assets/img/loading.svg' ) ),
					'action'      => 'process_contact_form',
				] );
			}
		}

		/**
		 * Process the contact for submission
		 *
		 * @return void
		 */
		public function process_contact_form() {
			wp_verify_nonce( $_POST['_contact_nonce'], 'process_contact_form' );
			$errors          = [];
			$data            = [];
			$input_whitelist = [
				'fname',
				'lname',
				'email',
				'msg',
			];

			// For each post key/value, validate and/or cleanse data accordingly
			foreach ( $_POST as $name => $value ) {
				if ( in_array( $name, $input_whitelist, false ) ) {
					switch ( $name ) {
						case 'email':
							$value = sanitize_email( $value );
							if ( empty( $value ) || ! is_email( $value ) ) {
								$errors[ $name ] = $value;
							} else {
								$data[ $name ] = $value;
							}
							break;

						case 'fname':
						case 'lname':
						case 'msg':
							$value = sanitize_text_field( $value );
							if ( empty( $value ) ) {
								$errors[ $name ] = $value;
							} else {
								$data[ $name ] = $value;
							}
							break;
					}
				}
			}
			// If there are form validation errors, return to the form, otherwise build the
			// email messages and send to appropriate parties
			if ( ! empty( $errors ) ) {
				wp_send_json( [
					'status'  => 'errors',
					'payload' => $errors,
				], 200 );
				die;
			} else {
				$this->send_msg( $data );
			}
		}

		/**
		 * Send email message
		 *
		 * @param array $data $_POST data
		 *
		 * @return void
		 */
		public function send_msg( $data ) {
			// Compose email to MBB
			$msg       = stripslashes( $data['msg'] );
			$fname     = stripslashes( $data['fname'] );
			$lname     = stripslashes( $data['lname'] );
			$email     = $data['email'];
			$payload   = [];
			$subject   = 'Message from monarchbaseball.com';
			$to        = sanitize_email( ( ! empty( $this->contact_form_settings['send_to'] ) ) ? $this->contact_form_settings['send_to'] : 'damon.sharp@gmail.com' );
			$msg       = wp_kses_post( sprintf( "The following message was sent through the monarchbaseball.com website.\n\n%s\n\nRegards,\n\n%s %s", $msg, $fname, $lname ) );
			$headers[] = "From: Monarch Baseball <{$this->contact_form_settings['sent_from']}>";
			$headers[] = sprintf( 'Reply-to: %s %s <%s>', $fname, $lname, $email );

			$mbb_email_sent = wp_mail( $to, $subject, $msg, $headers );

			// Email to monarch baseball sent, so let's return a payload
			// to show a message to the user.
			if ( $mbb_email_sent ) {
				$payload = [
					'status' => 'success',
				];
			} else {
				$payload = [
					'status'  => 'errors',
					'payload' => [],
				];
			}
			wp_send_json( $payload );
		}
	}

	MBB_Contact_Form::instance();

endif;
