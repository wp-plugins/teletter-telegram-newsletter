<?php
/**
 * @package Websima Telegram Bot
 * @version 1
 */
/*
Plugin Name: Teletter Telegram Newsletter
Plugin URI: http://websima.com/teletter
Description: Send Newsletter from Telegram Bot, user can subscribe to your site from Telegram Bot.
Author: Websima Creative Agency
Version: 1
Author URI: http://websima.com
*/
add_action( 'plugins_loaded', 'tbot_load_textdomain' );
function tbot_load_textdomain() {
  load_plugin_textdomain( 'tbot', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
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
if($time>wp_next_scheduled('getupdates')) {
getupdates();
}
add_site_option( 'lastupdateid', '1');

?>