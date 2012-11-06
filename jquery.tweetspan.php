<?php
/**
 * @package Hello_Dolly
 * @version 1.6
 */
/*
Plugin Name: jQuery.tweetspan
Plugin URI: https://github.com/substrakt/jquery.tweetspan
Description: Pull updates from a Twitter account straight into an HTML element. Check out test.html for a simple example.
Author: Mark Steadman
Version: 0.1
Author URI: http://substrakt.co.uk/
*/

if(!is_admin()) {
	wp_register_script(
		'jquery.tweetspan',
		plugins_url('jquery.tweetspan.min.js', __file__),
		array('jquery'),
		0.1,
		true
	);
}

function jquery_tweetspan_wp_head() {
	wp_enqueue_script('jquery.tweetspan');
}

add_filter('wp_head', 'jquery_tweetspan_wp_head');