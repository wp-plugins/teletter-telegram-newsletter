<?php
/**
 * @package Websima Telegram Bot
 * @version 1.1
 */
/*
Plugin Name: Teletter Telegram Newsletter
Plugin URI: http://websima.com/teletter
Description: Send Newsletter from Telegram Bot, user can subscribe to your site from Telegram Bot.
Author: Websima Creative Agency
Version: 1.1
Author URI: http://websima.com
*/
add_action( 'plugins_loaded', 'tbot_load_textdomain' );
function tbot_load_textdomain() {
  load_plugin_textdomain( 'tbot', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
function sendmessagebot ($user_id,$message) {
	$options = get_option( 'tbot_settings' );
    $token = $options['tbot_text_token'];
	if ($token) {
	$url = 'https://api.telegram.org/bot'.$token.'/sendMessage';
	$data = array('chat_id' => $user_id,'text' => $message);
		$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$update = file_get_contents($url, false, $context);
	//end send message
	}
}
function sendphotobot ($user_id,$photo) {
	$options = get_option( 'tbot_settings' );
    $token = $options['tbot_text_token'];
	if ($token) {
	$url = 'https://api.telegram.org/bot'.$token.'/sendPhoto';
	$data = array('chat_id' => $user_id,'photo' => $photo);
		$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$update = file_get_contents($url, false, $context);
	//end send message
	}
}
function sendnewsbot ($message,$offset,$limit) {
	// Send a message to user to know that subscriptions is activated
	$options = get_option( 'tbot_settings' );
	$token = $options['tbot_text_token'];
	$users = $options['tbot_select_users'];
	// Get All Subscribers
	if ($users == 'all') {
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'offset'         => $offset,
	);
	} else if ($users == 'active') {
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'meta_key' => 'activity',
	'meta_value' => 'active',
	'meta_compare' => '==',
	'offset'         => $offset,
	);	
	} else {
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'offset'         => $offset,
	);
	}


// The Query
$query = new WP_Query( $args );

// The Loop
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		//Message to every user
		$chat_id = get_the_title();
		sendmessagebot ($chat_id,$message);
   }
} 

wp_reset_postdata();
}
function sendphotonewsbot ($photo,$offset,$limit) {
	// Send a message to user to know that subscriptions is activated
	$options = get_option( 'tbot_settings' );
	$token = $options['tbot_text_token'];
	$users = $options['tbot_select_users'];
	// Get All Subscribers
	if ($users == 'all') {
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'offset'         => $offset,
	);
	} else if ($users == 'active') {
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'meta_key' => 'activity',
	'meta_value' => 'active',
	'meta_compare' => '==',
	'offset'         => $offset,
	);	
	} else {
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'offset'         => $offset,
	);
	}


// The Query
$query = new WP_Query( $args );

// The Loop
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		//Message to every user
		$chat_id = get_the_title();
		sendphotobot ($chat_id,$photo);
   }
} 

wp_reset_postdata();
}
function sendadminmessagebot ($message) {
	// Send a message to Admin to alert for subscription changes
	$options = get_option( 'tbot_settings' );
	$token = $options['tbot_text_token'];
	// Get All Admins
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => $limit,
	'meta_key' => 'isadmin',
	'meta_value' => 'yes',
	'meta_compare' => '==',
	);	
// The Query
$query = new WP_Query( $args );

// The Loop
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();
		//Message to every user
		$chat_id = get_the_title();
		sendmessagebot ($chat_id,$message);
   }
} 

wp_reset_postdata();
}

include_once('includes/api-settings.php');
include_once('includes/pagetemplate.php');
include_once('includes/users-save.php');
include_once('includes/getupdates.php');
include_once('includes/publishsend.php');
include_once('includes/notification-metabox.php');


$options = get_option( 'tbot_settings' );
$method = $options['tbot_select_method'];
$currency = $options['tbot_select_updates_time'];
if ($method == 'getupdates') {
	// Add function to register event to WordPress init
	add_action( 'init', 'getupdates_cron'); 

	// Function which will register the event
	function getupdates_cron() {
$options = get_option( 'tbot_settings' );
$currency = $options['tbot_select_updates_time'];
    // Make sure this event hasn't been scheduled
		if( !wp_next_scheduled( 'getupdates' ) ) {
        // Schedule the event
		if ($currency == 'hourly') {
			wp_schedule_event( time(), 'hourly', 'getupdates' );
		} elseif ($currency == 'twicedaily') {
			wp_schedule_event( time(), 'twicedaily', 'getupdates' );
		} elseif ($currency == 'daily') {
			wp_schedule_event( time(), 'daily', 'getupdates' );
		}
        
		}
	}
} else {
	$timestamp = wp_next_scheduled( 'getupdates' );
	// Un-schedule the event
	wp_unschedule_event( $timestamp, 'getupdates' );
}
$time = time();
$time = $time+100;
if(wp_next_scheduled('getupdates') && $time>wp_next_scheduled('getupdates')) {
add_action( 'init', 'getupdates' );
}
add_site_option( 'lastupdateid', '1');

?>