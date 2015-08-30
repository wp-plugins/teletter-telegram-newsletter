<?php
// Scheduled Action Hook
function getupdates() {
$options = get_option( 'tbot_settings' );
$token = $options['tbot_text_token'];
$currency = $options['tbot_select_updates_time'];
$method = $options['tbot_select_method'];
$limit = $options['tbot_select_limit'];
$welcome = $options['tbot_text_newsubscriber'];
if($welcome == '') {$welcome = 'Successfully Subscribed';}
$offset = get_site_option( 'lastupdateid' );
$offset = $offset+1;
if($token) {
$url = 'https://api.telegram.org/bot'.$token.'/getUpdates';
if ($offset == 1) {
	if ($limit == "nolimit") {
		$data = array();
	} else {
		$data = array('limit' => $limit);
	}
	
} else {
	if ($limit == "nolimit") {
		$data = array('offset' => $offset);
	} else {
		$data = array('offset' => $offset,'limit' => $limit);
	}
	
}
$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    )
);

$context  = stream_context_create($options);
$update = file_get_contents($url, false, $context);
$results = json_decode($update, true);
//print_r ($results);

$status = $results['ok'];
if($status == 1) {
	$newupdates = $results['result'];
	$number = count($newupdates);
	$i = 0;
foreach ($newupdates as $update) {
    //print_r ($value);
	$update_id = $update['update_id'];
	$from = $update['message']['from'];
	$user_id = $from['id'];
	$first_name = $from['first_name'];
	$last_name = $from['last_name'];
	$user_name = $from['username'];
	$date = $update['message']['date'];
	$text = $update['message']['text'];
	$post_date = date_i18n( 'Y-m-d H:i:s', $date, true );	
	if ($text == '/start') {
		$checker = get_page_by_title( $user_id,  ARRAY_A, 'subscriber' );
		if (!$checker){
	// Create post object
		$my_post = array(
		'post_type'     => 'subscriber',
		'post_title'    => $user_id,
		'post_status'   => 'publish',
		'post_date_gmt' => $post_date,
		);
	// Insert the post into the database
		$post_id = wp_insert_post( $my_post, $wp_error );
		update_post_meta ($post_id,'first_name',$first_name);
		update_post_meta ($post_id,'last_name',$last_name);
		update_post_meta ($post_id,'username',$user_name);
		update_post_meta ($post_id,'activity','active');
	// Send a message to user to know that subscriptions is activated
	$url = 'https://api.telegram.org/bot'.$token.'/sendMessage';
	$data = array('chat_id' => $user_id,'text' => $welcome);
		$options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
		)
	);

	$context  = stream_context_create($options);
	$updated = file_get_contents($url, false, $context);
	//end send message
		}
	}
	$i++;
	if ($i == $number) {
		update_site_option( 'lastupdateid', $update_id ); //Grab the last update for cronjob
	}
} //end foreach
} //end if status is ok
}
	}
?>