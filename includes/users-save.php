<?php
// Register Subscribers Post Type
function subscriber_post_type() {
	$labels = array(
		'name'                => __( 'subscribers', 'tbot' ),
		'singular_name'       => __( 'subscriber', 'tbot' ),
		'menu_name'           => __( 'subscribers', 'tbot' ),
		'name_admin_bar'      => __( 'subscribers', 'tbot' ),
		'parent_item_colon'   => __( 'Parent Item:', 'tbot' ),
		'all_items'           => __( 'All Items', 'tbot' ),
		'add_new_item'        => __( 'Add New Item', 'tbot' ),
		'add_new'             => __( 'Add New', 'tbot' ),
		'new_item'            => __( 'New Item', 'tbot' ),
		'edit_item'           => __( 'Edit Item', 'tbot' ),
		'update_item'         => __( 'Update Item', 'tbot' ),
		'view_item'           => __( 'View Item', 'tbot' ),
		'search_items'        => __( 'Search Item', 'tbot' ),
		'not_found'           => __( 'Not found', 'tbot' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'tbot' ),
	);
	$args = array(
		'label'               => __( 'subscriber', 'tbot' ),
		'description'         => __( 'Telegram Bot Subscribers', 'tbot' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'custom-fields', ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => false,
		'show_in_menu'        => false,
		'menu_position'       => 5,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
	);
	register_post_type( 'subscriber', $args );

}
add_action( 'init', 'subscriber_post_type', 0 );
// Add Subscribers Custom Column
add_filter( 'manage_edit-subscriber_columns', 'subscribers_columns' ) ;

function subscribers_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Subscriber ID' ,'tbot' ),
		'fname' => __( 'First Name' ,'tbot'),
		'lname' => __( 'Last Name' ,'tbot'),
		'uname' => __( 'Username' ,'tbot'),
		'activity' => __( 'Status' ,'tbot'),
		'isadmin' => __( 'Admin' ,'tbot'),
		'date' => __( 'Subscribe Date' ,'tbot')
	);

	return $columns;
}

add_action( 'manage_subscriber_posts_custom_column', 'my_manage_subscriber__columns', 10, 2 );

function my_manage_subscriber__columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'duration' column. */
		case 'fname' :

			/* Get the post meta. */
			$fname = get_post_meta( $post_id, 'first_name', true );

			/* If no duration is found, output a default message. */
			if ( empty( $fname ) )
				echo __( 'Unknown','tbot');

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf($fname );

			break;

		case 'lname' :

			/* Get the post meta. */
			$lname = get_post_meta( $post_id, 'last_name', true );

			/* If no duration is found, output a default message. */
			if ( empty( $lname ) )
				echo __( 'Unknown' ,'tbot');

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf($lname );

			break;

			case 'uname' :

			/* Get the post meta. */
			$uname = get_post_meta( $post_id, 'username', true );

			/* If no duration is found, output a default message. */
			if ( empty( $uname ) )
				echo __( 'Unknown' ,'tbot');

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf('@'.$uname );

			break;
			case 'activity' :

			/* Get the post meta. */
			$activity = get_post_meta( $post_id, 'activity', true );

			/* If no duration is found, output a default message. */
			if ( empty( $activity ) )
				echo __( 'Unknown' ,'tbot');

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf($activity );

			break;
			case 'isadmin' :

			/* Get the post meta. */
			$activity = get_post_meta( $post_id, 'isadmin', true );

			/* If no duration is found, output a default message. */
			if ( empty( $activity ) )
				echo __( 'subscriber' ,'tbot');

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo __( 'admin' ,'tbot');

			break;
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
?>