<?php 
function register_scripts () {
	wp_register_script('modernizr', get_bloginfo('template_directory') . '/js/vendor/modernizr-2.6.1.min.js', array(), '2.6.1');
	wp_register_style('screen-css', get_bloginfo('template_directory') . '/css/screen.css', array(), false, 'screen');
	wp_register_style('print-css', get_bloginfo('template_directory') . '/css/print.css', array(), false, 'print');
	
	wp_register_style('videojs', get_bloginfo('template_directory') . '/js/vendor/video-js/video-js.min.css', array(), false, 'screen');
	
	wp_register_style('mediaelement', get_bloginfo('template_directory') . '/js/vendor/media-element/build/mediaelementplayer.min.css', array(), false, 'screen');
	wp_register_style('believe-me', get_bloginfo('template_directory') . '/css/modules/believe-me.css', array(), false, 'screen');
	
	wp_register_script('twitter-widgets', '//platform.twitter.com/widgets.js', array(), false, true);
	wp_register_script('facebook-api', '//connect.facebook.net/en_US/all.js', array(), false, true);
	
	if (!is_admin()) {
		wp_enqueue_script('modernizr');
		if (!is_login_page()) {
			wp_enqueue_style('screen-css');
			wp_enqueue_style('print-css');		
		}
	}
}

//register all scripts here
add_action('init', 'register_scripts');