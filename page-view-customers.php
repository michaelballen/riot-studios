<?php
if (is_user_logged_in()) :
?>
<!DOCTYPE html>
<html>
	<head>
		<title>View Customers</title>
	</head>
	<body>
		<h1>Customers</h1>
		
		<?php
		$qry = new WP_Query(array(
			'post_type'=>'customer',
			'posts_per_page'=>40
		));
		?>
		<ul>
		<?php
		if ($qry->have_posts()) :
			while($qry->have_posts()) : $qry->the_post();
				$id = get_the_ID();
				$meta = get_post_meta($id);
				foreach ($meta as $k=>$v) {
					if ($k === 'shipping_address') {
						$meta[$k] = (array) json_decode($v[0]);
					} else {
						$meta[$k] = $v[0];
					}
				}
				print_r($meta);
		?>
				<li><?php echo get_post_meta($id, 'email', true); ?></li>
		<?php	
			endwhile;
		endif;
		?>
		</ul>
	</body>
</html>
<?php
endif;
?>