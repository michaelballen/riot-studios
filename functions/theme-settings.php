<?php
function setup_page ($arr=array()) {
	if (!is_array($arr)) {
		$arr = array(
			$arr
		);
	}
	foreach($arr as $v){
		if (!get_page_by_title($v)) {
			$page['post_type']    = 'page';
			$page['post_content'] = '';
			$page['post_parent']  = 0;
			$page['post_author']  = $user_ID;
			$page['post_status']  = 'publish';
			$page['post_title']   = $v;
			$pageid = wp_insert_post ($page);
		}
	}
}
//add the submenu to wodrpress general settings
function setup_theme_admin_menus () {  
    add_submenu_page(
		'options-general.php',
		'Riot Studios Settings',
		'Riot Settings',
		'manage_options',
		'riot-studios-options',
		'riot_options_html'
	);
}
//html for the settings page
function riot_options_html () {
	// Check that the user is allowed to update options  
	if (!current_user_can('manage_options')) {  
	    wp_die('You do not have sufficient permissions to access this page.');  
	}
	if (isset($_POST["update_settings"])) {  
	    // Do the saving
		//list of settings to check for...
		$save_inputs = array(
			'show_landing_page',
			'riot_description',
			'rioter_description',
			'riot_keywords',
			'google_analytics_id',
			'riot_shipping_intl',
			'riot_shipping_domestic'
		);
		if (!empty($_FILES['riot_image']) && $_FILES['riot_image']['size'] > 0) {
			$upload = wp_handle_upload($_FILES['riot_image'], array( 'test_form' => false ));
			update_option('riot_image', $upload['url']);
		}
		foreach ($save_inputs as $v) {
			if (isset($_POST[$v])) {
				update_option($v, $_POST[$v]);
			}
		}?>
	<div id="message" class="updated">Settings saved</div>
	<?php
	}
	?>  
	    <div class="wrap">
	        <?php screen_icon('themes'); ?> <h2>Front page elements</h2>  
	        <form method="POST" action="" enctype="multipart/form-data">
	            <table class="form-table">
					<tr>
	                    <th scope="row">
	                        <label for="show_landing_page">
	                            Show Landing Page When Not Logged In?
	                        </label>
	                    </th>
	                    <td>
							<?php
							$show_landing_page = get_option('show_landing_page');
							?>
							<select name="show_landing_page">
								<option value="yes" <?php if ($show_landing_page === 'yes') {
									echo 'selected';
								} ?>>Yes</option>
								<option value="no" <?php if ($show_landing_page !== 'yes') {
									echo 'selected';
								} ?>>No</option>
							</select>
	                    </td>
	                </tr>
	                <tr>
	                    <th scope="row">
	                        <label for="riot_description">
	                            General Site Description
	                        </label>
	                    </th>
	                    <td>
	                        <textarea rows="3" class="widefat" name="riot_description"><?= get_option('riot_description'); ?></textarea>
	                    </td>
	                </tr>
					<tr>
	                    <th scope="row">
	                        <label for="rioter_description">
	                            Description for Rioters
	                        </label>
	                    </th>
	                    <td>
	                        <textarea rows="3" class="widefat" name="rioter_description"><?= get_option('rioter_description'); ?></textarea>
	                    </td>
	                </tr>
					<tr>
						<th>
							<label for="riot_keywords">
								Meta Keywords (separate by ,)
							</label>
						</th>
						<td>
							<input type="text" name="riot_keywords" class="widefat" value="<?= get_option('riot_keywords'); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="riot_shipping_domestic">
								Shipping Base Price (Domestic)
							</label>
						</th>
						<td>
							<input type="text" name="riot_shipping_domestic" value="<?= get_option('riot_shipping_domestic'); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="riot_shipping_intl">
								Extra shipping per item for International Orders
							</label>
						</th>
						<td>
							<input type="text" name="riot_shipping_intl" value="<?= get_option('riot_shipping_intl'); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="riot_image">
								Site Image
							</label>
						</th>
						<td>
							<img src="<?= get_option('riot_image'); ?>" style="max-height:200px; max-width:200px; width:auto; height:auto;" />
							<input type="file" name="riot_image" />
						</td>
					</tr>
	            </table>
				<input type="hidden" name="update_settings" value="1">
				<input type="submit" value="Save" class="button button-primary">
	        </form>
	    </div>
	<?php
}
//setup the pages we need to run the site
function is_login_page() {
	$current = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
	$current .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return strpos($current, wp_login_url()) === false ? false : true;
}
function theme_features () {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'menus' );
	register_nav_menu('Footer', 'Footer Nav Menu');
}
function check_login () {
	$current = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';
	$current .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$home = home_url() . '/';
	if (get_option('show_landing_page') === 'yes' && !is_user_logged_in() && !is_login_page() && $current !== $home && strpos($current, '/api') === false) {
		header('Location: ' . home_url());
		exit();
	}
	if ($home === 'http://test.riotstudios.com/' && !is_user_logged_in() && !is_login_page()) {
		header('Location: ' . wp_login_url($current));
		exit();
	}
}
function make_pages () {
	global $wp_rewrite;
	setup_page(array(
		'Media',
		'About',
		'Store',
		'Template',
		'View Customers',
		'View Invoices',
		'Register',
		'Theater'
	));
	$wp_rewrite->flush_rules();
}
function custom_login_css () {
?>
    <link rel="stylesheet" id="custom_riot_admin_css"  href="<?php echo get_bloginfo( 'template_directory' ) . '/css/modules/login-page.css'; ?>" type="text/css" media="all" />
<?php
}//end custom_login_css fn

function riot_login_logo_url() {
    return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'riot_login_logo_url' );

function riot_login_logo_url_title() {
    return 'Riot Studios';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );
//set the login css
add_action( 'login_enqueue_scripts', 'custom_login_css' );

// run function when wordpress creates the admin menus
add_action("admin_menu", "setup_theme_admin_menus");
add_action("init", "check_login");
add_action("switch_theme", "make_pages");
add_action("after_setup_theme", "theme_features");