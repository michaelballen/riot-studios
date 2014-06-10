<?php

function riot_attachments( $attachments )
{
	$args = array(
		// title of the meta box (string)
		'label'         => 'Attachments',
		// all post types to utilize (string|array)
		'post_type'     => array( 'post', 'page', 'team_member', 'image' ),
		// allowed file type(s) (array) (image|video|text|audio|application)
		'filetype'      => array('image'),
		// include a note within the meta box (string)
		'note'          => 'Attach files here!',
		// text for 'Attach' button in meta box (string)
		'button_text'   => __( 'Attach Files', 'attachments' ),
		// text for modal 'Attach' button (string)
		'modal_text'    => __( 'Attach', 'attachments' ),
		
		/**
		 * Fields for the instance are stored in an array. Each field consists of
		 * an array with three keys: name, type, label.
		 *
		 * name  - (string) The field name used. No special characters.
		 * type  - (string) The registered field type.
		 *                  Fields available: text, textarea
		 * label - (string) The label displayed for the field.
		 */
		'fields'        => array(
			array(
				'name'  => 'title',                          // unique field name
				'type'  => 'text',                           // registered field type
				'label' => __( 'Title', 'attachments' ),     // label to display
			),
			array(
				'name'  => 'caption',                        // unique field name
				'type'  => 'textarea',                       // registered field type
				'label' => __( 'Caption', 'attachments' ),   // label to display
			)
		)
	);
	$attachments->register( 'riot_attachments', $args ); // unique instance name
}
add_action( 'attachments_register', 'riot_attachments' );