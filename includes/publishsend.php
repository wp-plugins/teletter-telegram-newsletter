<?php
function sendmessage_publish($post_ID,$offset,$limit) {

	   $type = get_post_meta ($post_ID,'tbot_select',true);
	   $custommessage = get_post_meta ($post_ID,'tbot_text',true);
	   
	   $url = get_permalink($post_ID); 
	   if ($type == 'url'){
		   $message = $url;
	   } elseif ($type == 'customurl'){
		   $message = $custommessage.' '.$url;
	   } elseif ($type == 'customtext') {
		   $message = $custommessage;
	   }
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

// Restore original Post Data
wp_reset_postdata();
 //  }
}
?>