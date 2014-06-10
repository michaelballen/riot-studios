<div class="checkout-theater-content">
	<section id="payment-success">
		<h2>Payment Complete</h2>
		<button class="btn btn-warning big" data-close_checkout><?php _e('&laquo; Shop Some More') ?></button>
		<a class="btn btn-primary big receipt-btn" data-ajax_load href="#"><?php _e('View Receipt &raquo;') ?></a>
	</section>
	
	<section id="checkout-shopping-cart">
		<h2>Shopping Cart</h2>
		<ul>
			{{#each items}}
			<li data-id="{{this.id}}">
				<div class="img-con">
					<img src="{{this.thumb}}" alt="{{this.title}}">
				</div>
				<div class="text-con">
					<h3>{{this.title}}</h3>
					<span>Qty: {{this.qty}}</span><span>//</span><span>Price: ${{this.price}}</span>
				</div>
				<button class="remove-btn" data-remove="{{this.id}}">&times;</button>
			</li>
			{{/each}}
			<li data-id="shipping" class="other group">
				<div class="text-con">
					<h3>Shipping</h3>
					<span class="ship-amt">${{shipping}}</span>
				</div>
			</li>
			<li data-id="shipping" class="other group">
				<div class="text-con">
					<h3>Total</h3>
					<span class="total-amt">${{total}}</span>
				</div>
			</li>
		</ul>
	</section>
	<form action="<?php echo site_url('api'); ?>" class="checkout-form">

		<section id="checkout-form-contact">

			<h2>Personal Info</h2>
			<div class="control-group">
				<label for="name">Name</label>
				<input type="text" name="user_name" value="{{personal_info.user_name}}" class="save">
			</div>
			
			<div class="control-group">
				<label for="email">Email</label>
				<input type="email" name="user_email" value="{{personal_info.user_email}}" class="save">
			</div>

			<div class="control-group">
				<label for="phone">Phone</label>
				<input type="tel" name="user_phone" value="{{personal_info.user_phone}}" class="save">
			</div>
			
			<label class="checkbox">
			      <input type="checkbox" class="save" name="email_subscribe" checked> Stay updated on Riot Studios
			</label>

		</section>

		<section id="checkout-form-shipping">
			<h2>Shipping Address</h2>

			<label for="shipping_country">Country</label>
			<select name="shipping_country" class="save">
			<?php $Riot->doCountryOptions(); ?>
			</select>
			
			<div class="control-group">
				<label for="shipping_address1">Address 1</label>
				<input type="text" name="shipping_address1" value="{{personal_info.shipping_address1}}" class="save">
			</div>

			<label for="shipping_address2">Address 2</label>
			<input type="text" name="shipping_address2" value="">

			<div class="group">
				<div class="span1of2 control-group">
					<label for="shipping_city">City</label>
					<input type="text" name="shipping_city" value="{{personal_info.shipping_city}}" class="city save">
				</div>

				<div class="span1of2">
					<label for="shipping_state">State</label>
					<select class="state save" value="{{personal_info.shipping_state}}" name="shipping_state">
					<?php $Riot->doStateOptions(); ?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label for="shipping_city">Zip</label>
				<input type="text" name="shipping_zip" value="{{personal_info.shipping_zip}}" class="save">
			</div>

			<label class="checkbox">
			      <input type="checkbox" class="save" name="same_address" {{#if personal_info.same_address}}checked{{/if}}> Same as Billing
			</label>
		</section>


		<section id="checkout-form-billing" {{#if personal_info.same_address}}class="hidden"{{/if}}>
			<h2>Billing Address</h2>

			<label for="billing_country">Billing Country</label>
			<select name="billing_country">
			<?php $Riot->doCountryOptions(); ?>
			</select>
			<div class="control-group">
				<label for="billing_address1">Address 1</label>
				<input type="text" name="billing_address1" value="">
			</div>

			<label for="billing_address2">Address 2</label>
			<input type="text" name="billing_address2" value="">

			<div class="group">
				<div class="span1of2 control-group">
					<label for="billing_city">City</label>
					<input type="text" name="billing_city" value="{{personal_info.billing_city}}" class="save">
				</div>
				<div class="span1of2">
					<label for="billing_state">State</label>
					<select value="{{personal_info.shipping_state}}" name="billing_state" class="save">
					<?php $Riot->doStateOptions(); ?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label for="billing_zip">Zip</label>
				<input type="text" name="billing_zip" value="">
			</div>
		</section>

		<section id="checkout-form-card">
			<h2>Credit Card</h2>
			<div class="control-group">
				<label for="billing_name">Name On Card</label>
				<input type="text" name="billing_name" value="{{personal_info.user_name}}" id="input-billing-name">
			</div>
			<div class="control-group">
				<label>Card Number</label>
				<input type="number" size="20" autocomplete="off" class="card-number"/>
			</div>
			<div class="control-group half-desktop half-desktop-first">
				<label>CVC</label>
				<input type="number" size="4" autocomplete="off" class="card-cvc" />
			</div>

			<div class="control-group half-desktop">
				<label>
					<span>Expiration</span>
				</label>
				<div class="card-expiry">
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
			    	<span class="card-expiry-slash"> / </span>
					<select class="card-expiry-year">
						<?php $Riot->doYearOptions(); ?>
					</select>
				</div>
			</div>
		</section>
		
		<input type="hidden" name="action" value="storeCheckout">
		
		<button class="btn btn-success submit-btn" type="button" id="checkout-submit-btn">Checkout &raquo;</button>
	</form>
</div>