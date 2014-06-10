<?php

$module = "checkout";
$Riot->page_description = "Checkout from the Riot Studios store";

if (!$Riot->isAjax()) ://if not ajax
get_header();
?>
<div class="default-content">
<?php endif;//end if not ajax ?>

<div class="content-outer about" data-module="<?php echo $module; ?>" data-title="<?php $Riot->do_title(); ?>">
	<div class="content-inner">
		<div class="gray-texture">
			<h1><?php the_title(); ?></h1>
			<form action="">
				<label for="name">Name</label>
				<input type="text" name="name" value="">
				
				<label for="email">Email</label>
				<input type="email" name="email" value="">
				
				<label for="phone">Phone</label>
				<input type="tel" name="phone" value="">
				
				<h2>Shipping Address</h2>
				
				<label for="shipping_country">Country</label>
				<select value="shipping_country">
				<?php $Riot->doCountryOptions(); ?>
				</select>
				
				<label for="shipping_address1">Address 1</label>
				<input type="text" name="address1" value="">
				
				<label for="shipping_address2">Address 2</label>
				<input type="text" name="shipping_address2" value="">
				
				<label for="shipping_city">City</label>
				<input type="text" name="shipping_city" value="">
				
				<label for="shipping_state">State</label>
				<select value="shipping_state">
				<?php $Riot->doStateOptions(); ?>
				</select>
				
				<label for="shipping_city">Zip</label>
				<input type="text" name="shipping_zip" value="">
				
				<label class="checkbox">
				      <input type="checkbox"> Same as Billing
				</label>
				
				<h2>Billing Address</h2>
				
				<label for="billing_country">Billing Country</label>
				<select value="shipping_country">
				<?php $Riot->doCountryOptions(); ?>
				</select>
				
				<label for="billing_address1">Address 1</label>
				<input type="text" name="address1" value="">
				
				<label for="billing_address2">Address 2</label>
				<input type="text" name="billing_address2" value="">
				
				<label for="billing_city">City</label>
				<input type="text" name="billing_city" value="">
				
				<label for="billing_state">State</label>
				<select value="billing_state">
				<?php $Riot->doStateOptions(); ?>
				</select>
				
				<label for="billing_city">Zip</label>
				<input type="text" name="billing_zip" value="">
				
				<label for="billing_name">Name On Card</label>
				<input type="text" name="billing_name" value="">
				
				<label>Card Number</label>
				<input type="text" size="20" autocomplete="off" class="card-number"/>
				
				<label>CVC</label>
				<input type="text" size="4" autocomplete="off" class="card-number"/>
				
				<label>
					<span>Expiration</span>
					<select class="card-expiry-month">
						<option value="01">01</option>
						<option value="02">02</option>
						<option value="03">03</option>
						<option value="04">04</option>
						<option value="05">05</option>
						<option value="06">06</option>
						<option value="07">07</option>
						<option value="08">08</option>
						<option value="09">09</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</label>
				    <span> / </span>
					<select class="card-expiry-year">
						<?php $Riot->doYearOptions(); ?>
					</select>
				<a href="#" class="btn btn-primary">Checkout</a>
			</form>
		</div>
	<!-- end .content-inner -->
	</div>
<!-- end .content-outer -->
</div>

	
<?php
if (!$Riot->isAjax())://if not ajax
?>
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