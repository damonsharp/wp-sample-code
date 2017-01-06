<?php

if ( ! class_exists( 'MBB_Twitter' ) ) :

	/**
	 * Monarch Baseball Twitter
	 *
	 * Integrate with the Twitter API for tweet retrieval
	 */
	class MBB_Twitter {

		/**
		 * Bearer/Oauth token endpoint
		 *
		 * @var string
		 */
		private $bearer_token_request_uri = 'https://api.twitter.com/oauth2/token';

		/**
		 * Twitter request endpoint with placeholders
		 *
		 * @var string
		 */
		private $timeline_request_uri = 'https://api.twitter.com/1.1/statuses/user_timeline.json?count=50&screen_name=%s&exclude_replies=true&include_rts=false';

		/**
		 * Instance of the class
		 * @var object
		 */
		private static $instance;

		/**
		 * Construct
		 *
		 * Nothing to see here.
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
				self::$instance = new MBB_Twitter;
			}
			return self::$instance;
		}

		/**
		 * Main method to get tweets
		 *
		 * @param  integer $count the number of tweets to get
		 * @param  string  $transient_key transient key for caching
		 *
		 * @return array formatted set of tweets
		 */
		public function get_tweets( $count = 0, $transient_key = 'tweets' ) {
			if ( false === ( $tweets = MBB_Helpers()->get_transient( $transient_key ) ) || empty( $tweets ) ) {
				// Encode consumer keys
				$bearer_token_credentials = $this->get_bearer_token_credentials();
				$bearer_token = '';
				// If we have bearer credentials, get the bearer token
				if ( ! empty( $bearer_token_credentials ) ) {
					$bearer_token = $this->get_bearer_token_from_credentials( $bearer_token_credentials );
				}
				// Authenticate API request using bearer token
				if ( false !== $bearer_token ) {
					// Get tweet count from passed in value or from
					// themes settings as a fallback
					if ( 0 === $count ) {
						$count = ( ! empty( $settings_count = $this->get_twitter_setting( 'tweet_count' ) ) ) ? $settings_count : 5;
					}
					$tweets = $this->get_tweets_with_bearer_token( $bearer_token, $count, $transient_key );
				}
			}
			return $tweets;
		}

		/**
		 * Get bearer token credentials
		 *
		 * @return string base 64 encoded string
		 */
		private function get_bearer_token_credentials() {
			$consumer_key = $this->get_twitter_setting( 'consumer_key' );
			$consumer_secret = $this->get_twitter_setting( 'consumer_secret' );
			$bearer_token_credentials = '';
			if ( ! empty( $consumer_key ) && ! empty( $consumer_secret ) ) {
				$bearer_token_credentials = base64_encode( "$consumer_key:$consumer_secret" );
			}
			return $bearer_token_credentials;
		}

		/**
		 * Get bearer token from API
		 *
		 * @param string $bearer_token_credentials basic auth creds
		 * @return string access/bearer token|| boolean
		 */
		private function get_bearer_token_from_credentials( $bearer_token_credentials ) {
			$bearer_token = wp_remote_post(
				$this->bearer_token_request_uri,
				[
					'decompress' => false,
					'headers' => [
						'Authorization' => "Basic $bearer_token_credentials",
						'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
						'Accept-Encoding' => '',
					],
					'body' => 'grant_type=client_credentials',
				]
			);
			if ( ! is_wp_error( $bearer_token ) ) {
				$response_body = wp_remote_retrieve_body( $bearer_token );

				if ( ! empty( $response_body ) ) {
					$response_body_obj = json_decode( $response_body );
					if ( ! empty( $response_body_obj->access_token ) && 'bearer' === $response_body_obj->token_type ) {
						return $response_body_obj->access_token;
					}
				}
			}
			return false;
		}

		/**
		 * Get tweets from api, formatted and cached
		 *
		 * @param  string $bearer_token token from API
		 * @param  integer $count the number of tweets
		 * @param  string $transient_key the key to set the transient by
		 * @return array of tweets
		 */
		private function get_tweets_with_bearer_token( $bearer_token, $count, $transient_key ) {
			$tweets = [];
			$twitter_username = $this->get_twitter_setting( 'username' );
			$url = sprintf( $this->timeline_request_uri, $twitter_username );
			$tweets_response = wp_remote_get(
				$url,
				[
					'headers' => [
						'Authorization' => "Bearer $bearer_token",
						'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
					],
				]
			);
			if ( ! is_wp_error( $tweets_response ) ) {
				$tweets = json_decode( wp_remote_retrieve_body( $tweets_response ) );
				if ( ! empty( $tweets ) ) {
					$tweets = $this->get_formatted_tweets( $tweets, $count );
					MBB_Helpers()->set_transient( $transient_key, $tweets, 600 );
				}
			}
			return $tweets;
		}

		/**
		 * Format the returned tweets from the API and return them
		 *
		 * @param  object $tweets the tweets object
		 * @param  integer $count number to return
		 *
		 * @return array formatted tweets
		 */
		private function get_formatted_tweets( $tweets, $count ) {
			$formatted_tweets = [];
			if ( ! empty( $tweets ) ) {
				$i = 1;
				foreach ( $tweets as $tweet ) {
					//Convert urls to <a> links
					$formatted_tweet = preg_replace( "/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet->text );
					//Convert hashtags to twitter searches in <a> links
					$formatted_tweet = preg_replace( "/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $formatted_tweet );
					//Convert attags to twitter profiles in &lt;a&gt; links
					$formatted_tweet = preg_replace( "/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $formatted_tweet );
					// Collect an array of formatted tweets
					$formatted_tweets[ $i ]['id'] = ( ! empty( $tweet->id ) ) ? $tweet->id : 0;
					$formatted_tweets[ $i ]['text'] = $formatted_tweet;
					$formatted_tweets[ $i ]['created_at'] = ( ! empty( $tweet->created_at ) ) ? MBB_Helpers()->get_formatted_timestamp( $tweet->created_at ) : '';
					$formatted_tweets[ $i ]['name'] = ( ! empty( $tweet->user->name ) ) ? $tweet->user->name : '';
					$formatted_tweets[ $i ]['username'] = ( ! empty( $tweet->user->screen_name ) ) ? $tweet->user->screen_name : '';
					$formatted_tweets[ $i ]['userurl'] = ( ! empty( $tweet->user->screen_name ) ) ? 'https://twitter.com/' . $tweet->user->screen_name : 'https://twitter.com';
					$formatted_tweets[ $i ]['avatar'] = ( ! empty( $tweet->user->profile_image_url_https ) ) ? $tweet->user->profile_image_url_https : '';
					// We're grabbing 50 tweets, which should be sufficient,
					// then limiting the number returned by passing in a count
					if ( $count === $i ) {
						break;
					}
					$i++;
				}
			}
			return $formatted_tweets;
		}

		/**
		 * Get twitter settings from the DB
		 *
		 * @param string $key setting to get
		 * @return string the returned setting
		 */
		private function get_twitter_setting( $key ) {
			$twitter_setting = MBB_Query()->get_twitter_setting( $key );
			if ( empty( $twitter_setting ) ) {
				return;
			}
			return rawurlencode( $twitter_setting );
		}

	}

	function MBB_Twitter() {
		return MBB_Twitter::instance();
	}

endif;
