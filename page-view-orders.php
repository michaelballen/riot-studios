<?php
if (!is_user_logged_in()) :
	header("Location: " . wp_login_url(site_url('view-orders')));
else :
?>
<!DOCTYPE html>
<html>
	<head>
		<title>View Orders</title>
	</head>
	<body>
		<h1>Orders</h1>
		
		<?php
		$qry = new WP_Query(array(
			'post_type'=>'invoice',
			'posts_per_page'=>40,
			'meta_query'=>array(
				array(
					'key'=>'status',
					'value'=>'complete',
					'compare'=>'='
				)
			)
		));
		?>
		<ul>
		<?php
		if ($qry->have_posts()) :
			while($qry->have_posts()) : $qry->the_post();
			
				$id = get_the_ID();
				$meta = get_post_meta($id);
				
				print_r($meta);
				
				$time = get_the_time('F j, Y h:i A');
				foreach ($meta as $k=>$v) {
					if ($k === 'shipping_address' || $k === 'items') {
						$meta[$k] = (array) json_decode($v[0]);
					} else {
						$meta[$k] = $v[0];
					}
				}
				$items = $meta['items'];
				$customer_id = $meta['customer'];
		?>
				<li>
					<h3>Order #<?php echo $id; ?></h3>
					<p><?php echo $time; ?></p>
					<h4>Customer</h4>
					<?php
					//get the customer in db
					echo get_post_meta($customer_id, 'name', true) . ' - ' . get_post_meta($customer_id, 'email', true);
					?>		
					<h4>Items</h4>
					<ol>
						<?php foreach($items as $k=>$v) : ?>
						<li>
							<span><?php echo $v->title; ?> - </span>
							<span>Qty: <?php echo $v->qty; ?></span>
							<span>Price: <?php echo $v->price; ?></span>
						</li>
						<?php endforeach; ?>
						<?php if (!empty($meta['shipping'])) : ?>
						<li class="shipping">
							Shipping: $<?php echo $meta['shipping'] / 100; ?>
						</li>
						<?php endif; ?>
						<li class="total">
							Total: $<?php echo $meta['total'] / 100; ?>
						</li>
					</ol>
				</li>
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