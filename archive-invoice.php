<?php
global $custom_page_title, $user_ID, $module, $wp_query;

get_currentuserinfo();
if (!is_user_logged_in()) { 
	header('Location: ' . wp_login_url($Riot->currentURL()));
	exit(); 
}

$custom_page_title = 'Invoices';
$module = 'admin';
if (isset($_REQUEST['meta_query'])) {
	$invoiceqry = new WP_Query(array(
		'post_type'=>'invoice',
		'posts_per_page'=>100,
		'meta_query' => array(
				array(
					'key' => $_REQUEST['meta_key'],
					'value' => $_REQUEST['meta_value'],
					'compare' => '='
				)
			),
		//'orderby'=>'meta_value',
		//'meta_key' => 'user_last_name',
		'paged'=>$_REQUEST['admin_page']
	));
} else {
	$invoiceqry = new WP_Query(array(
		'post_type'=>'invoice',
		'posts_per_page'=>100,
		//'orderby'=>'meta_value',
		//'meta_key' => 'user_last_name',
		'paged'=>$_REQUEST['admin_page']
	));
}

get_header('black');
?>
<div class="default-content" data-module="<?php echo $module; ?>"></div>
	<div class="content-outer" data-title="<?php $Riot->do_title(); ?>" id="">
		<div class="content-inner">
			<header class="group admin-header">
				<h1 class="fltlt open-sans">Viewing Invoices</h1>
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
							<li><a href="<?php echo site_url('invoices'); ?>">All</a></li>
						</ul>
					</div>
				</div>
			</header>
			<div>
			<?php
			$current_invoice;
			if ($invoiceqry->have_posts()) :
				while($invoiceqry->have_posts()) : $invoiceqry->the_post();
						$current_invoice = get_post($post->ID);
			?>
				<div class="admin-view-item">
						<h3 class="open-sans"><a href="<?php the_permalink(); ?>" target="_blank"><span><?php
						the_ID();
?></span></a></h3>
					<ul class="actions" data-secret_value="" data-id_key="invoice_id" data-nonce_key="invoice_nonce" data-nonce_value="<?php echo wp_create_nonce('riotInvoice'); ?>" data-admin_edit_data_parent data-edit_id="<?php echo get_the_ID(); ?>" data-action="saveInvoice">
						<li><a href="#star" class="<?php ?>" data-admin_edit_status data-key="starred" data-toggle_class="active" data-on="1" data-off="0" data-value="<?php  ?>"><span class="ifont ifontstar"></span></a></li>
						<li><a href="#viewed" class="<?php ?>" data-admin_edit_status data-key="admin_viewed" data-toggle_class="active" data-on="1" data-off="0" data-value="<?php  ?>"><span class="ifont ifonteye"></span></a></li>
						<li><a href="#" <?php  ?>><span class="ifont ifontcheckmark"></span></a></li>
					</ul>
				</div>
			<?php
				endwhile;
			endif;
			?>
			
			<div class="pagination">
				<?php
					$current_page = !empty($_REQUEST['admin_page']) ? $_REQUEST['admin_page'] : 1;
					$max_pages = $invoiceqry->max_num_pages;
					if ($current_page > 1) {
					?>
					<a href="?admin_page=<?php echo $current_page - 1; ?>" class="btn"><span class="ifontarrow-left"></span> Prev</a>
					<?php
					}
					if ($current_page < $max_pages - 1) {
					?>
					<a href="?admin_page=<?php echo $current_page + 1; ?>" class="btn">Next <span class="ifontarrow-right"></span></a>
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