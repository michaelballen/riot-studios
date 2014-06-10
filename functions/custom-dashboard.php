<?php
// Custom WordPress Footer
function remove_footer_admin () {
    echo '&copy; 2013 - Riot Studios';
}
add_filter('admin_footer_text', 'remove_footer_admin');

// remove stupid widgets
add_action('wp_dashboard_setup', 'wpc_dashboard_widgets');
function wpc_dashboard_widgets() {
	global $wp_meta_boxes;
	//QuickPress
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	//QuickPress
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	//QuickPress
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	// Today widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	// Last comments
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	// Incoming links
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	// Plugins
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
}
function add_twitter_contactmethod( $contactmethods ) {
	// Add Twitter
	$contactmethods['twitter'] = 'Twitter';
	// Remove Yahoo IM
	unset($contactmethods['yim']);
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	return $contactmethods;
}
function save_extra_profile_fields ($user_id) {
	if (empty($_POST['twitter'])) {
		return false;
	}
	$twitter = new Twitter($_POST['twitter']);
	$prof_pic = $twitter->getUserInfo('profile_image_url');
	update_usermeta( $user_id, 'twitter_prof_pic', $prof_pic );
}
add_filter('user_contactmethods','add_twitter_contactmethod',10,1);

add_action( 'personal_options_update', 'save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_profile_fields' );