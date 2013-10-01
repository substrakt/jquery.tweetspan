<?php

/**
 * @package jQuery_tweetspan
 * @version 0.2
 */
/*
Plugin Name: jQuery.tweetspan
Plugin URI: https://github.com/substrakt/jquery.tweetspan
Description: Pull updates from a Twitter account straight into an HTML element. Check out test.html for a simple example. Based on <a href="https://github.com/abraham/twitteroauth">TwitterOAuth</a>.
Author: Mark Steadman
Version: 0.2
Author URI: http://substrakt.co.uk/
*/

require_once('includes/OAuth.php');
require_once('includes/twitteroauth.php');

if(defined('WPLANG')) {
	class TweetspanWidget extends WP_Widget {
		function TweetspanWidget() {
			parent::__construct(
				'jquery_tweetspan',
				'Tweetspan',
				array(
					'description' => __('Show a number of updates from your Twitter account', 'jquery_tweetspan'),
				)
			);
		}
		
		function widget($args, $instance) {
			extract($args);
			echo $before_widget;
			
			if(!isset($instance['username'])) {
				$instance['username'] = 'substrakt';
			}
			
			if(!isset($instance['count'])) {
				$instance['count'] = 5;
			} ?>
			
			<div class="tweets" data-account="<?php echo $instance['username']; ?>" data-count="<?php echo intVal($instance['count']); ?>">
				<h1>Tweets from @<a href="http://twitter.com/<?php echo $instance['username']; ?>"><?php echo $instance['username']; ?></a></h1>
				<div class="tweet">
					<img class="avatar" data-field="profile_image_url" />
					<p data-field="text" data-format="tweet"></p>
					
					<p>
						<small>
							<span data-field="created_at" data-format="timesince capfirst"></span>
						</small>
					</p>
				</div>
			</div>
			
			<?php echo $after_widget;
		}
		
		function update($new_instance, $old_instance) {
			$instance = array();
			$username = $new_instance['username'];
			
			if($username) {
				if(substr($username, 0, 1) == '@') {
					$username = substr($username, 1);
				}
			}
			
			$instance['username'] = $username;
			$instance['count'] = intVal($new_instance['count']);
			
			return $instance;
		}

		function form($instance) {
			$username = isset($instance['username']) ? $instance['username'] : 'substrakt';
			$count = isset($instance['count']) ? $instance['count'] : 5; ?>
			
			<p>
				<label for="<?php echo $this->get_field_name('username'); ?>"><?php _e('Username:'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_name('count'); ?>"><?php _e('Number of tweets to show:'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo intVal($count); ?>" />
			</p>
		<?php }
	}
	
	if(!is_admin()) {
		wp_register_script(
			'jquery.tweetspan',
			plugins_url('jquery.tweetspan.js', 'jquery.tweetspan/jquery.tweetspan.php'),
			array('jquery'),
			false,
			false
		);
	}
	
	function jquery_tweetspan_wp_head() {
		wp_enqueue_script('jquery.tweetspan');
	}
	
	function jquery_tweetspan_wp_footer() {
		$endpoint = get_option('jquery_tweetspan_endpoint');
		if(!$endpoint) {
			$endpoint = plugins_url('jquery.tweetspan.php', 'jquery.tweetspan/jquery.tweetspan.php') . '?platform=wordpress';
		} ?>
		
		<script>
			jQuery.tweetspan('endpoint', '<?php echo $endpoint; ?>');
		</script>
	<?php }
	
	function jquery_tweetspan_register_widgets() {
		register_widget('TweetspanWidget');
	}
	
	function jquery_tweetspans_admin_menu() {
		add_submenu_page(
			'options-general.php',
			'jQuery Tweetspan',
			'jQuery Tweetspan',
			'administrator',
			__FILE__,
			'jquery_tweetspan_settings_page'
		);
	}
	
	function jquery_tweetspan_settings_page() { ?>
		<div class="wrap">
			<h2>jQuery Tweetspan</h2>
			
			<form method="post" action="options.php">
			   	<?php settings_fields('jquery_tweetspan'); ?>
				<?php do_settings_sections('jquery_tweetspan'); ?>
				
				<?php submit_button(); ?>
			</form>
		</div>
	<?php }
	
	function jquery_tweetspans_admin_init() {
		register_setting('jquery_tweetspan', 'jquery_tweetspan_consumer_key');
		register_setting('jquery_tweetspan', 'jquery_tweetspan_consumer_secret');
		register_setting('jquery_tweetspan', 'jquery_tweetspan_access_key');
		register_setting('jquery_tweetspan', 'jquery_tweetspan_access_secret');
		register_setting('jquery_tweetspan', 'jquery_tweetspan_endpoint');
		
		add_settings_field('jquery_tweetspan_consumer_key',
			'Consumer Key',
			'jquery_tweetspan_consumer_key',
			'jquery_tweetspan', 'oauth'
		);
		
		add_settings_field('jquery_tweetspan_consumer_secret',
			'Consumer Secret',
			'jquery_tweetspan_consumer_secret',
			'jquery_tweetspan', 'oauth'
		);
		
		add_settings_field('jquery_tweetspan_access_token_key',
			'Access Token Key',
			'jquery_tweetspan_access_key',
			'jquery_tweetspan', 'oauth'
		);
		
		add_settings_field('jquery_tweetspan_access_token_secret',
			'Access Token Secret',
			'jquery_tweetspan_access_secret',
			'jquery_tweetspan', 'oauth'
		);
		
		add_settings_field('jquery_tweetspan_endpoint',
			'Endpoint URL',
			'jquery_tweetspan_endpoint_url',
			'jquery_tweetspan', 'endpoint'
		);
		
		add_settings_section('oauth',
			'Local endpoint settings',
			'jquery_tweetspan_settings_oauth',
			'jquery_tweetspan'
		);
		
		add_settings_section('endpoint',
			'Endpoint',
			'jquery_tweetspan_settings_endpoint',
			'jquery_tweetspan'
		);
	}

	function jquery_tweetspan_settings_oauth() { ?>
		<p>
			You can either use this plugin to communicate with Twitter, or a third-party service. To use
			this plugin, you&rsquo;ll need to <a href="https://dev.twitter.com/apps/new">create a Twitter app</a>
			and pass in the <strong>consumer key</strong>, <strong>consumer secret</strong>,
			<strong>access key</strong> and <strong>access token</strong> into the boxes below.
		</p>
	<?php }
	
	function jquery_tweetspan_settings_endpoint() { ?>
		<p>
			If you want to use a third-party endpoint to talk to Twitter, put in the URL below, otherwise
			this plugin will act as the endpoint and pass search queries from this site to Twitter.
		</p>
	<?php }
	
	function jquery_tweetspan_consumer_key() { ?>
		<input name="jquery_tweetspan_consumer_key" id="jquery_tweetspan_consumer_key" type="text" autocomplete="off" class="code" value="<?php echo get_option('jquery_tweetspan_consumer_key'); ?>" />
	<?php }
	
	function jquery_tweetspan_consumer_secret() { ?>
		<input name="jquery_tweetspan_consumer_secret" id="jquery_tweetspan_consumer_secret" type="password" autocomplete="off" class="code" value="<?php echo get_option('jquery_tweetspan_consumer_secret'); ?>" />
	<?php }
	
	function jquery_tweetspan_access_key() { ?>
		<input name="jquery_tweetspan_access_key" id="jquery_tweetspan_access_key" type="text" autocomplete="off" class="code" value="<?php echo get_option('jquery_tweetspan_access_key'); ?>" />
	<?php }

	function jquery_tweetspan_access_secret() { ?>
		<input name="jquery_tweetspan_access_secret" id="jquery_tweetspan_access_secret" type="password" autocomplete="off" class="code" value="<?php echo get_option('jquery_tweetspan_access_secret'); ?>" />
	<?php }
	
	function jquery_tweetspan_endpoint_url() {
		if(!function_exists('curl_init')) { ?>
			<div class="error"><p>You need the <a href="http://php.net/manual/en/book.curl.php">PHP cURL</a> library to use a local endpoint.</p></div>
		<?php } ?>
		
		<input name="jquery_tweetspan_endpoint" id="jquery_tweetspan_endpoint" type="text" value="<?php echo get_option('jquery_tweetspan_endpoint'); ?>" placeholder="<?php echo plugins_url('jquery.tweetspan.php', 'jquery.tweetspan/jquery.tweetspan.php'); ?>?platform=wordpress" />
	<?php }
	
	add_filter('init', 'jquery_tweetspan_wp_head');
	add_action('widgets_init', 'jquery_tweetspan_register_widgets');
	add_action('admin_init', 'jquery_tweetspans_admin_init');
	add_action('admin_menu', 'jquery_tweetspans_admin_menu');
	add_filter('wp_footer', 'jquery_tweetspan_wp_footer');
}

if(isset($_GET['from']) || isset($_GET['q'])) {
	if(isset($_GET['platform'])) {
		$platform = $_GET['platform'];
		unset($_GET['platform']);
		
		switch($platform) {
			case 'wordpress':
				require_once('../../../wp-blog-header.php');
				header("HTTP/1.1 200 OK");
				
				define('TWITTER_CONSUMER_KEY',
					trim(get_option('jquery_tweetspan_consumer_key'))
				);
				
				define('TWITTER_CONSUMER_SECRET',
					trim(get_option('jquery_tweetspan_consumer_secret'))
				);
				
				define('TWITTER_ACCESS_KEY',
					trim(get_option('jquery_tweetspan_access_key'))
				);
				
				define('TWITTER_ACCESS_SECRET',
					trim(get_option('jquery_tweetspan_access_secret'))
				);
				
				break;
		}
	}
	
	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$twitter = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_KEY, TWITTER_ACCESS_SECRET);
	$twitter->format = null;
	$response = $twitter->get($url, $_GET);
	echo $response;
}