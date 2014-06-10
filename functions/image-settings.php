<?php

add_image_size( 'admin-list-thumb', 100, 100, true );
add_image_size( 'medium-square', 320, 320, true );
add_image_size( 'large-square', 500, 500, true );
add_image_size( 'home-slide', 1600, 600, true );

// Add the posts and pages columns filter. They can both use the same function.
add_filter('manage_posts_columns', 'add_post_thumbnail_column', 5);
//add_filter('manage_pages_columns', 'tcb_add_post_thumbnail_column', 5);

// Add the column
function add_post_thumbnail_column( $cols ){
	$colsstart = array_slice( $cols, 0, 1, true );
	$colsend   = array_slice( $cols, 1, null, true );
	
	$colls = array_merge(
		$colsstart,
		array( 'riot_post_thumb' => __('Image') ),
		$colsend,
		array( 'riot_author' => __('Author') )
	);
	return $colls;
}

// Hook into the posts an pages column managing. Sharing function callback again.
add_action('manage_posts_custom_column', 'display_post_thumbnail_column', 5, 2);
//add_action('manage_pages_custom_column', 'tcb_display_post_thumbnail_column', 5, 2);

// Grab featured-thumbnail size post thumbnail and display it.
function display_post_thumbnail_column($col, $id){
	switch($col){
		case 'riot_post_thumb':
			if( function_exists('the_post_thumbnail') ) {
				echo '<a href="' . get_edit_post_link() . '">';
				the_post_thumbnail( 'admin-list-thumb' );
				echo '</a>';
			} else {
				echo 'Not supported in theme';
			}
			break;
		case 'riot_author':
			echo get_the_author_meta('user_firstname') . ' ' . get_the_author_meta('user_lastname');
			break;
	}
}