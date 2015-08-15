<?php
function sendmessage_publish($post_ID) {

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
	// Get All Subscribers
	$args = array (
	'post_type'              => array( 'subscriber' ),
	'pagination'             => false,
	'posts_per_page'         => '-1',
	'meta_key' => 'activity',
	'meta_value' => 'active',
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
		$url = 'https://api.telegram.org/bot'.$token.'/sendMessage';
		$data = array('chat_id' => $chat_id,'text' => $message);
		$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$updated = file_get_contents($url, false, $context);
	$results = json_decode($updated, true);
	//remove deactive users
	$status = $results['ok'];
	if (!$status == 'false') {
	$page = get_page_by_title( $chat_id,OBJECT,'subscriber' );
	update_post_meta ($page->ID,'activity','deactive');
	}
   }
} 

// Restore original Post Data
wp_reset_postdata();
 //  }
}
?>