<?php
add_action( 'admin_menu', 'tbot_add_admin_menu' );
add_action( 'admin_init', 'tbot_settings_init' );


function tbot_add_admin_menu(  ) { 

	add_options_page( __('Telegram API', 'tbot'), __('Telegram API', 'tbot'), 'manage_options', 'telegram_api', 'telegram_api_options_page' );

}


function tbot_settings_init(  ) { 

	register_setting( 'pluginPage', 'tbot_settings' );

	add_settings_section(
		'tbot_pluginPage_section', 
		__( 'Get Telegram API ready', 'tbot' ), 
		'tbot_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'tbot_text_token', 
		__( 'Enter Your Telegram Bot Token', 'tbot' ), 
		'tbot_text_token_render', 
		'pluginPage', 
		'tbot_pluginPage_section' 
	);
	add_settings_field( 
		'tbot_select_method', 
		__( 'Use getUpdates or Webhook', 'tbot' ), 
		'tbot_select_method_render', 
		'pluginPage', 
		'tbot_pluginPage_section' 
	);
    add_settings_field( 
		'tbot_select_updates_time', 
		__( 'getUpdates Recurrence', 'tbot' ), 
		'tbot_select_updates_time_render', 
		'pluginPage', 
		'tbot_pluginPage_section' 
	);
	add_settings_field( 
		'tbot_select_limit', 
		__( 'getUpdates Limit in every recurrence', 'tbot' ), 
		'tbot_select_limit_render', 
		'pluginPage', 
		'tbot_pluginPage_section' 
	);
	add_settings_field( 
		'tbot_text_newsubscriber', 
		__( 'Welcome Message to New Subscriber', 'tbot' ), 
		'tbot_text_newsubscriber_render', 
		'pluginPage', 
		'tbot_pluginPage_section' 
	);
	
$args = array(
   'public'   => true
);
$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'
$post_types = get_post_types( $args, $output, $operator ); 
foreach ( $post_types  as $post_type ) {
	if ($post_type == 'attachment' || $post_type == 'subscriber') {} else {
	add_settings_field( 
		'tbt_'.$post_type, 
		__( 'Send Notification for ', 'tbot' ).$post_type, 
		'tbot_post_types_render', 
		'pluginPage', 
		'tbot_pluginPage_section',
array( 'type' => $post_type )		
	);
	}
}

}

function tbot_text_token_render(  ) { 
	$options = get_option( 'tbot_settings' );
	?>
	<input type='text' name='tbot_settings[tbot_text_token]' value='<?php echo $options['tbot_text_token']; ?>'>
	<?php

}
function tbot_select_method_render(  ) { 

	$options = get_option( 'tbot_settings' );
	?>
	<select name='tbot_settings[tbot_select_method]'>
		<option value='getupdates' <?php selected( $options['tbot_select_method'], 'getupdates' ); ?>><?php printf(__( 'getUpdates', 'tbot' )); ?></option>
		<option value='manual' <?php selected( $options['tbot_select_method'], 'manual' ); ?>><?php printf(__( 'Manual', 'tbot' )); ?></option>
		<option disabled value='webhook' <?php selected( $options['tbot_select_method'], 'webhook' ); ?>><?php printf(__( 'Webhook', 'tbot' )); ?></option>
	</select>

<?php

}
function tbot_select_updates_time_render(  ) { 

	$options = get_option( 'tbot_settings' );
	?>
	<select name='tbot_settings[tbot_select_updates_time]'>
		<option value='hourly' <?php selected( $options['tbot_select_updates_time'], 'hourly' ); ?>><?php printf(__( 'Hourly', 'tbot' )); ?></option>
		<option value='twicedaily' <?php selected( $options['tbot_select_updates_time'], 'twicedaily' ); ?>><?php printf(__( 'Twice Daily', 'tbot' )); ?></option>
		<option value='daily' <?php selected( $options['tbot_select_updates_time'], 'daily' ); ?>><?php printf(__( 'Daily', 'tbot' )); ?></option>
	</select>

<?php

}
function tbot_select_limit_render(  ) { 

	$options = get_option( 'tbot_settings' );
	?>
	<select name='tbot_settings[tbot_select_limit]'>
		<option value='nolimit' <?php selected( $options['tbot_select_limit'], 'nolimit' ); ?>><?php printf(__( 'No Limit', 'tbot' )); ?></option>
		<option value='100' <?php selected( $options['tbot_select_limit'], '100' ); ?>><?php printf(__( '100 new updates', 'tbot' )); ?></option>
		<option value='50' <?php selected( $options['tbot_select_limit'], '50' ); ?>><?php printf(__( '50 new updates', 'tbot' )); ?></option>
		<option value='20' <?php selected( $options['tbot_select_limit'], '20' ); ?>><?php printf(__( '20 new updates', 'tbot' )); ?></option>
	</select>

<?php

}
function tbot_text_newsubscriber_render(  ) { 
	$options = get_option( 'tbot_settings' );
	?>
	<textarea cols='40' rows='5' name='tbot_settings[tbot_text_newsubscriber]'><?php echo $options['tbot_text_newsubscriber']; ?></textarea>
	<?php

}

function tbot_post_types_render( $type ) { 
$type = $type['type'];
	$options = get_option( 'tbot_settings' );
	?>
	<input type='checkbox' name='tbot_settings[tbot_<?php echo $type; ?>]' <?php checked( $options['tbot_'.$type], $type ); ?> value='<?php echo $type; ?>'>
	<label><?php echo $type; ?></label>
	<?php
}

function tbot_settings_section_callback(  ) { 

	echo __( 'Before updating this settings please read the Documents carefully', 'tbot' );

}


function telegram_api_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2><?php printf(__( 'Telegram API', 'tbot' )); ?></h2>
		<a style="left: 5%;position: absolute;"href="http://websima.com/teletter" title="websima teletter"><img width="281" height="364" src="<?php echo plugins_url( 'teletter.png', __FILE__ ); ?>" alt="وبسیما تلتر"/></a>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>