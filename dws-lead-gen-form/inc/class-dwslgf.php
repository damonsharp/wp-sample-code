<?php

if ( ! class_exists( 'DWSLGF' ) ) :

	/**
	 * Customers
	 *
	 * Create custom post type, taxonomies, and metaboxes for customers
	 *
	 * @uses  WordPress Fieldmanager plugin (http://fieldmanager.org)
	 */
	class DWSLGF {

		/**
		 * The post type.
		 *
		 * @var string the post type.
		 */
		private $post_type = DWSLGF_PREFIX . '_customer';

		/**
		 * Instance of the class.
		 *
		 * @var DWSLGF $instance
		 */
		private static $instance;

		/**
		 * DWSLGF constructor.
		 */
		private function __construct() {
			/* Don't do anything, needs to be initialized via instance() method */
		}

		/**
		 * Create one instance of the class
		 *
		 * @return DWSLGF instance of the class
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new DWSLGF;
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Fire off the meta box, etc. creation here
		 */
		public function init() {
			add_shortcode( 'dws_lead_gen_form', [ $this, 'setup_shortcode' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'add_scripts_styles' ] );
			add_action( 'wp_ajax_dwslgf', [ $this, 'process_dwslgf_submission' ] );
			add_action( 'wp_ajax_nopriv_dwslgf', [ $this, 'process_dwslgf_submission' ] );
		}

		public function setup_shortcode( $atts ) {
			$attributes = shortcode_atts( [
				'name_label'        => __( 'Full Name', 'adwslgf' ),
				'name_maxlength'    => '',
				'phone_label'       => __( 'Phone Number', 'adwslgf' ),
				'phone_maxlength'   => '10',
				'email_label'       => __( 'Email Address', 'adwslgf' ),
				'email_maxlength'   => '',
				'budget_label'      => __( 'Desired Budget', 'adwslgf' ),
				'budget_maxlength'  => '',
				'message_label'     => __( 'Message', 'adwslgf' ),
				'message_maxlength' => '',
				'message_rows'      => '',
				'message_cols'      => '',
				'submit_label'      => __( 'Submit', 'adwslgf' ),
			], $atts );

			$timezone = get_option( 'timezone_string' );
			if ( empty( $timezone ) ) {
				$timezone = 'America/New_York';
			}

			// User Carbon library for date/time.
			$dt = Carbon\Carbon::now( $timezone );

			?>
			<form id="dwslgf-form" method="POST">
				<p>All fields required.</p>
				<div>
					<label for="dwslgf-name"><?php echo esc_html( $attributes['name_label'] ); ?></label>
					<input id="dwslgf-name" type="text" name="dwslgf_name"
						   maxlength="<?php echo esc_attr( $attributes['name_maxlength'] ); ?>">
				</div>
				<div>
					<label for="dwslgf-phone"><?php echo esc_html( $attributes['phone_label'] ); ?></label>
					<input id="dwslgf-phone" type="text" name="dwslgf_phone"
						   maxlength="<?php echo esc_attr( $attributes['phone_maxlength'] ); ?>">
				</div>
				<div>
					<label for="dwslgf-email"><?php echo esc_html( $attributes['email_label'] ); ?></label>
					<input id="dwslgf-email" type="text" name="dwslgf_email"
						   maxlength="<?php echo esc_attr( $attributes['email_maxlength'] ); ?>">
				</div>
				<div>
					<label for="dwslgf-budget"><?php echo esc_html( $attributes['budget_label'] ); ?></label>
					<input id="dwslgf-budget" type="text" name="dwslgf_budget"
						   maxlength="<?php echo esc_attr( $attributes['budget_maxlength'] ); ?>">
				</div>
				<div>
					<label for="dwslgf-message"><?php echo esc_html( $attributes['message_label'] ); ?></label>
					<textarea id="dwslgf-message" name="dwslgf_message"
							  rows="<?php echo esc_attr( $attributes['message_rows'] ); ?>"
							  cols="<?php echo esc_attr( $attributes['message_cols'] ); ?>"
							  maxlength="<?php echo esc_attr( $attributes['message_maxlength'] ); ?>"></textarea>
				</div>
				<div>
					<button id=" dwslgf-submit
					"
					type="submit"><?php echo esc_html( $attributes['submit_label'] ); ?></button>
				</div>
				<input type="hidden" name="dwslgf_submission_datetime"
					   value="<?php echo esc_html( $dt->toDateTimeString() ); ?>">
			</form>
			<?php
		}

		public function add_scripts_styles() {
			// Enqueue the needed scripts when using the shortcode.
			global $post;
			if ( has_shortcode( $post->post_content, 'dws_lead_gen_form' ) ) {
				wp_enqueue_style( 'dwslgf_styles', DWSLGF_PLUGIN_DIR_URL . 'assets/css/dwslgf.css' );
				wp_enqueue_script( 'dwslgf-submit', DWSLGF_PLUGIN_DIR_URL . 'assets/js/dwslgf.js', [ 'jquery' ], false, true );
				wp_localize_script( 'dwslgf-submit', 'dwslgf', [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'action'   => 'dwslgf',
					'nonce'    => wp_create_nonce( 'dwslgf_nonce' ),
				] );
			}
		}

		public function process_dwslgf_submission() {
			check_ajax_referer( 'dwslgf_nonce' );
			$post_data       = [];
			$errors          = [];
			$field_whitelist = [
				'dwslgf_name',
				'dwslgf_phone',
				'dwslgf_email',
				'dwslgf_budget',
				'dwslgf_message',
				'dwslgf_submission_datetime',
			];

			foreach ( $_POST as $key => $value ) {
				if ( in_array( $key, $field_whitelist ) ) {
					if ( empty( $value ) || ( 'dwslgf_email' === $key && ! is_email( $value ) ) ) {
						$errors[ $key ] = $value;
					} else {
						if ( 'dwslgf_email' === $key ) {
							$value = sanitize_email( $value );
						} else {
							$value = sanitize_text_field( $value );
						}
						$post_data[ $key ] = $value;
					}
				}
			}

			if ( ! empty( $errors ) ) {
				wp_send_json_error( [ 'msg' => __( 'Please check the fields above and re-submit!', 'dwslgf' ) ] );
			} else {
				$post_id = wp_insert_post( [
					'post_title'  => $post_data['dwslgf_name'],
					'post_type'   => $this->post_type,
					'post_status' => 'private',
				], true );

				if ( ! is_wp_error( $post_id ) ) {
					unset( $post_data['dwslgf_name'] );
					foreach ( $post_data as $meta_key => $meta_value ) {
						add_post_meta( $post_id, $meta_key, $meta_value );
					}
					wp_send_json_success( [ 'msg' => __( 'Thanks for submitting!', 'dwslgf' ) ] );
				}
			}

		}
	}

	DWSLGF::instance();

endif;