<?php
$module = "single";

//get the order data...
if (have_posts()) :
	while(have_posts()) : the_post();
		$id = get_the_ID();
		$items = get_post_meta($id, 'items', true);
		$items = json_decode($items);
		$shipping = (float) get_post_meta($id, 'shipping', true) / 100;
		$total = (float) get_post_meta($id, 'total', true) / 100;
		$stripe_order = $Riot->getStripeOrder(get_post_meta($id, 'stripe', true));
		$status_message = 'Payment received. Item not yet shipped';
		
		$customer_id = get_post_meta($id, 'customer', true);
		$customer = get_post_meta($customer_id);
		$customer_name = $customer['name'][0];
		
		$shipping_address = json_decode($customer['shipping_address'][0]);
				
	endwhile;
endif;

if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">
<?php endif; ?>
	<div class="content-outer invoice" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
			<div class="invoice-container">
				<header class="group">
					<h1><?php printf(__('Invoice %1$s'), get_the_ID()); ?></h1>
					<p class="date"><?php the_time('F j, Y'); ?></p>
				</header>
				
				<section class="items">
					<h2><?php _e('Items'); ?></h2>
					<ul>
					<?php foreach($items as $k=>$v): ?>
						<li class="group item">
							<img src="<?php echo $v->thumb; ?>" />
							<h3><?php echo $v->title; ?></h3>
							<p class="qty"><?php _e($v->qty . ' &times; $' . money_format('%i', $v->price)); ?></p>
						</li>
					<?php endforeach; ?>
					<?php
					if ($shipping > 0) :
					?>
						<li class="item group">
							<h3><?php _e('Shipping'); ?></h3>
							<p class="qty">$<?php echo money_format('%i', $shipping); ?></p>
						</li>
					<?php
					endif;
					?>
						<li class="total item group">
							<h3><?php _e('Total'); ?></h3>
							<p class="qty">$<?php echo money_format('%i', $total); ?></p>
						</li>
					</ul>
				</section>
				
				<section class="billing">
					<h2><?php _e('Billed To') ?></h2>
					<p><?php echo $customer_name . ' - ' . $stripe_order->card->type . ' &hellip;' . $stripe_order->card->last4; ?></p>
				</section>
				
				<section class="shipping">
					<h2><?php _e('Shipped To') ?></h2>
					<p><?php echo $customer_name; ?><br><?php echo $shipping_address->street; ?><br><?php if (!empty($shipping_address->street2)) {
						echo $shipping_address->street2 . '<br>';
					} echo $shipping_address->city . ', ' . $shipping_address->state . ' ' . $shipping_address->zip; ?>
					</p>
				</section>
			</div>
		<!--!end content inner-->	
		</div>
	<!--! end content outer-->
	</div>
<?php
if (!$Riot->isAjax()):
?>
		<div class="touch-outset"></div>
	<!--!end default content-->
	</div>
	<?php
	get_sidebar();
	get_footer();
else://if it is ajax
?>
	getRequireJS();
<?php
endif;//end if is ajax
?>