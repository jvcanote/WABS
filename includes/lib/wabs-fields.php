<?php
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function wabs_metaboxes( array $meta_boxes ) {
	// Example of all available fields

	$fields = array(
		array( 
			'id' => '_wabs_active',  
			'name' => 'Status', 
			'type' => 'checkbox', 
			'value' => 'Active', 
			'cols' => 2 ),

		array( 
			'id' => '_wabs_global',  
			'name' => 'Extend Display', 
			// 'desc' => __('Show this action bar everywhere.','wabs'),
			'type' => 'checkbox', 
			'value' => 'Global', 
			'save_callback' =>
				array( WABS(), 'save_global_id' ),
			'cols' => 2 ),

		array( 
			'id' => '_wabs_unique_id',  
			'name' => 'Unique ID', 
			'type' => 'text_small', 
			'default' =>  wp_generate_password( 8 ),
			'placeholder' => wp_generate_password( 8 ), 
			'cols' => 3 ),
		

		array( 
			'id' => '_wabs_background_color', 
			'name' => 'Background Color', 
			'type' => 'colorpicker', 
			'default' => '#1E73BE', 
			'cols' => 2 ),
		
		array( 
			'id' => '_wabs_text_color', 
			'name' => 'Text Color', 
			'type' => 'colorpicker', 
			'default' => '#FFFFFF', 
			'cols' => 2 ),
		

		array( 
			'id' => '_wabs_message',  
			'name' => 'Message', 
			'type' => 'wysiwyg', 
			'options' => array( 
				'editor_height' => '20' ), 
			'maxlength' => 60 ),

		
		array( 
			'id' => '_wabs_button_text',  
			'name' => 'Button Text', 
			'type' => 'text', 
			'placeholder' => 'Read More', 
			'cols' => 4 ),
		
		array( 
			'id' => '_wabs_link',  
			'name' => 'Button Link', 
			'type' => 'url', 
			'placeholder' => 'https://your-domain.com', 
			'cols' => 4 ),

		array( 
			'id' => '_wabs_target',  
			'name' => 'Open Link in New Window', 
			'type' => 'checkbox', 
			'value' => 'y', 
			'cols' => 4 ),

		array( 
			'id' => '_wabs_action_symbol', 
			'class' => 'hidden', 
			'name' => 'Hide Icon', 
			'type' => 'radio', 
			'options' => wabs_action_symbols() + array( 'without_action' => 'None' ),
		),

		array( 
			'id' => '_wabs_scheduled',  
			'name' => 'Schedule', 
			'type' => 'checkbox', 
			'value' => 'y', 
			'cols' => 4 ),

		array( 
			'id' => '_wabs_start_date', 
			'name' => 'Start Date', 
			'type' => 'datetime_unix', 
			'cols' => 4 ),
		
		array( 
			'id' => '_wabs_end_date', 
			'name' => 'End Date', 
			'type' => 'datetime_unix', 
			'cols' => 4 ),

	);

	$meta_boxes[] = array(
		'title' => 'Action Bar Scheduler',
		'pages' => 'page',
		'fields' => $fields
	);

	return $meta_boxes;

}
add_filter('cmb_meta_boxes', 'wabs_metaboxes' );
