<?php
global $custom_page_title, $user_ID, $module, $wp_query, $post;

get_currentuserinfo();
if (!is_user_logged_in()) { 
	header('Location: ' . wp_login_url($Riot->currentURL()));
	exit(); 
}

$custom_page_title = 'Job Apps';
$module = 'admin';
if (isset($_REQUEST['meta_query'])) {
	$appqry = new WP_Query(array(
		'post_type'=>'job_application',
		'posts_per_page'=>20,
		'meta_query' => array(
			array(
				'key' => $_REQUEST['meta_key'],
				'value' => $_REQUEST['meta_value'],
				'compare' => '='
			),
			array(
				'key' => 'user_email',
				'value' => '',
				'compare' => '!='
			)
		),
		'paged'=>$_REQUEST['app_page']
	));
} else {
	$appqry = new WP_Query(array(
		'post_type'=>'job_application',
		'posts_per_page'=>20,
		'paged'=>$_REQUEST['app_page'],
		'meta_query' => array(
			array(
				'key' => 'user_email',
				'value' => '',
				'compare' => '!='
			)
		),
		'paged'=>$_REQUEST['app_page']
	));
}

get_header('black');
?>
<div class="default-content" data-module="<?php echo $module; ?>"></div>
	<div class="content-outer" data-title="<?php $Riot->do_title(); ?>" id="">
		<div class="content-inner">
			<header class="group admin-header">
				<h1 class="fltlt open-sans">Viewing Applications</h1>
				<div class="right-actions fltrt">
					<span class="btn-group-title">Viewing:</span>
					<div class="btn-group pull-right" data-select_dropdown>
						<button class="btn" data-select_dropdown_title><?php
						if (isset($_REQUEST['view_label'])) {
							echo $_REQUEST['view_label'];
						} else {
							_e('All');
						}
						?></button>
						<button class="btn dropdown-toggle" data-toggle="dropdown">
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="?meta_query=1&meta_key=status&meta_value=complete&view_label=Complete">Complete</a></li>
							<li><a href="?meta_query=1&meta_key=starred&meta_value=1&view_label=Starred">Starred</a></li>
							<li><a href="?meta_query=1&meta_key=admin_viewed&meta_value=1&view_label=Viewed">Viewed</a></li>
							<li><a href="<?php echo site_url('job-apps'); ?>">All</a></li>
						</ul>
					</div>
				</div>
			</header>
			<div>
			<?php
			$current_app;
			if ($appqry->have_posts()) :
				while($appqry->have_posts()) : $appqry->the_post();
					$email = get_post_meta(get_the_ID(), 'user_email', true);
					$current_app = $Riot->getJobApplicationByID(get_the_ID());
			?>
				<div class="admin-view-item">
						<h3 class="open-sans"><a href="<?php echo $current_app->read_only_link; ?>" target="_blank"><?php echo $current_app->display_name; ?> <span><?php
						if (!empty($current_app->location)) {
							echo ' - ' . $current_app->location;
						}
?></span></a></h3>
					<ul class="actions" data-secret_key="application_secret" data-secret_value="<?php echo $current_app->secret; ?>" data-id_key="application_id" data-nonce_key="job_app_nonce" data-nonce_value="<?php echo wp_create_nonce('riotJobApplication'); ?>" data-admin_edit_data_parent data-edit_id="<?php echo $current_app->ID; ?>" data-action="saveJobApplication">
						<li><a href="#star" class="<?php
						if ($current_app->starred == "1") {
							echo 'active';
						} ?>" data-admin_edit_status data-key="starred" data-toggle_class="active" data-on="1" data-off="0" data-value="<?php echo $current_app->starred; ?>"><span class="ifont ifontstar"></span></a></li>
						<li><a href="#viewed" class="<?php
						if ($current_app->admin_viewed == "1") {
							echo 'active';
						} ?>" data-admin_edit_status data-key="admin_viewed" data-toggle_class="active" data-on="1" data-off="0" data-value="<?php echo $current_app->admin_viewed; ?>"><span class="ifont ifonteye"></span></a></li>
						<li><a href="#" <?php if ($current_app->status==="complete") {
							echo 'class="active"';
						} ?>><span class="ifont ifontcheckmark"></span></a></li>
					</ul>
				</div>
			<?php
				endwhile;
			else :
				
				echo 'NO POSTS!';
			?>
				
				
			<?php
			endif;
			?>
			
			<div class="pagination">
				<?php
					$current_page = !empty($_REQUEST['app_page']) ? $_REQUEST['app_page'] : 1;
					$max_pages = $appqry->max_num_pages;
					if ($current_page > 1) {
					?>
					<a href="?app_page=<?php echo $current_page - 1; ?>" class="btn"><span class="ifontarrow-left"></span> Prev</a>
					<?php
					}
					if ($current_page < $max_pages) {
					?>
					<a href="?app_page=<?php echo $current_page + 1; ?>" class="btn">Next <span class="ifontarrow-right"></span></a>
					<?php
					}
				?>
			</div>
			
			
			</div>
		<!--!end content inner-->	
		</div>
	<!--! end content outer-->
	</div>
<?php
get_footer('black');
?>