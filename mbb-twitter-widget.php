<?php

if ( ! class_exists( 'MBB_Widget_Tweets' ) ) :

	/**
	 * Widget for displaying tweets
	 */
	class MBB_Widget_Tweets extends WP_Widget {

		private $widget_slug  = 'mbb_widget_tweets';

		private $widget_title = 'MBB Latest Tweets';

		private $widget_desc  = 'Displays tweets from the Monarch Baseball account';

		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			$widget_opts = [
				'description' => $this->widget_desc,
			];
			parent::__construct( $this->widget_slug, $this->widget_title, $widget_opts );
		}

		/**
		 * Outputs the content of the widget to the user
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$count = ( ! empty( $instance['tweet_count'] ) ) ? intval( $instance['tweet_count'] ) : 0;
			$tweets = MBB_Twitter()->get_tweets( $count, "{$this->widget_slug}-{$args['id']}" );
			require_once( MBB_PLUGIN_DIR . 'template-parts/widgets/tweets.php' );
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title = ( ! empty( $instance['title'] ) ) ? wp_strip_all_tags( $instance['title'] ) : $this->widget_title;
			// Get the number of tweets to diplay.
			// First try from the widget, then settings, then a fallback default value
			$settings_tweet_count = MBB_Query()->get_twitter_setting( 'tweet_count' );
			if ( ! empty( $instance['tweet_count'] ) ) {
				$tweet_count = $instance['tweet_count'];
			} else {
				$tweet_count = ! empty( $settings_tweet_count ) ? $settings_tweet_count : 1;
			}
			$tweet_count = intval( $tweet_count );
			?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Widget Title</label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'tweet_count' ) ); ?>">Number of tweets to display:</label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'tweet_count' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'tweet_count' ) ); ?>" value="<?php echo esc_attr( $tweet_count ); ?>" size="4" maxlength="2">
				</p>
			<?php
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			// Clear transient if updating count
			$tweet_count = ( ! empty( $new_instance['tweet_count'] ) ) ? intval( $new_instance['tweet_count'] ) : '';
			if ( ! empty( $old_instance['tweet_count'] ) && ( intval( $old_instance['tweet_count'] ) !== intval( $tweet_count ) ) ) {
				delete_transient( 'tweets' );
			}
			// Process form values
			$instance = $old_instance;
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
			$instance['tweet_count'] = ( ! empty( $new_instance['tweet_count'] ) ) ? intval( $new_instance['tweet_count'] ) : '';
			return $instance;
		}
	}

endif;
