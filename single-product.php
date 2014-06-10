<?php
/*
Template Name: Store
*/
$module = 'store';

$selected_post;//empty var to hold post object info...
$products_qry = new WP_Query(array(
	'post_type'=> 'product'
));

//set the default product...
$selected_product = get_the_ID();
if (!$selected_product || get_post_type() === "page") {
	$selected_product = 29;
}
$Riot->page_description = 'Buy One Nation Under God / Beware of Christians DVDs. Stream videos online from Riot Studios for Name-Your-Price';
if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">	
<?php
endif;//end if is ajax
?>
	<div class="content-outer store" id="scrollable" data-title="<?php $Riot->do_title(); ?>" data-module="<?php echo $module; ?>">
		<div class="content-inner">
			<section id="product-slider" class="group">
				<a href="#" data-toggle_product_slider><span class="text"><?php _e('View Products'); ?></span><?php $Riot->do_three_lines(); ?></a>
				<a href="#" data-product_slider_left id="product-slider-arrow-left" class="off"><span class="ifontarrow-left"></span></a>
				<a href="#" data-product_slider_right id="product-slider-arrow-right" class="off"><span class="ifontarrow-right"></span></a>
				<div class="product-con group">
					<ul class="group unsized">
				<?php while ( $products_qry->have_posts() ) : $products_qry->the_post(); ?>
						<li id="product-<?php the_ID(); ?>" data-id="<?php the_ID(); ?>" class="<?php
						if (get_the_ID() == $selected_product) {
							echo "active";
						} ?>">
							<div class="img-holder">
							<?php if (has_post_thumbnail()) {
								$med_square = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium-square');

								// check if the largeImage width is equal to your setting of 800px
								if ($med_square[1] !== 320 || $med_square[2] !== 320) {
									// if it is output large size
									the_post_thumbnail('thumbnail', array(
										'class'=>'show-mobile'
									));
								} else {
									// if it isn't then show the full size image
									the_post_thumbnail('medium-square', array(
										'class'=>'show-mobile'
									));
								}
								the_post_thumbnail('medium', array(
									'class'=>'hide-mobile'
								));
							}//end if has post thumb ?>
							<div class="info-cover">
								<span class="ifontinfo"></span>
							</div>
							</div>
							<div class="triangle"></div>
							<h2><?php the_title(); ?></h2>
						</li>
				<?php endwhile; wp_reset_postdata(); ?>
					</ul>
				</div>
			</section>
				<?php
				$selected_qry = new WP_Query(array(
					'post_type' => "product",
					"p" => (string) $selected_product,
					'posts_per_page'=>1
				));
				while ( $selected_qry->have_posts() ) : $selected_qry->the_post();
					$nyop = get_post_meta(get_the_ID(), '_nyop', true);
					$price = (float) get_post_meta(get_the_ID(), '_price', true);
				?>
			<section id="store-main-display" class="group" data-meta='<?php echo $Riot->makePostMeta(); ?>'>
				<article>
					<section class="content">
						<h2><?php the_title(); ?></h2>
						<?php if (has_post_thumbnail()) { ?>
						<div class="side-con fltlt">
						<?php
							the_post_thumbnail('large');
						}//end if has post thumb ?>
							<div class="extra-con btn-group btn-group-vertical">
								<?php
								$trailer_link = get_post_meta($post->ID, '_trailer_link', true);
								$website_link = get_post_meta($post->ID, '_website_link', true);
								?>
								<a id="product-trailer-link" href="<?php echo $trailer_link; ?>" class="<?php if (empty($trailer_link)) {
									echo 'hidden ';
								} ?>btn" data-play_video="<?php echo get_post_meta($post->ID, '_trailer_id', true); ?>">Watch the Trailer</a>
								<a id="product-website-link" href="<?php echo $website_link; ?>" target="_blank" class="<?php if (empty($website_link)) {
									echo 'hidden ';
								} ?>btn">Visit the Website</a>
							</div>
						</div>
						<span class="price-con">
							<p class="bold">
								<?php if ($nyop == 1) {
									echo "Name Your Price!";
								} else {
									echo "$" . number_format($price, 2);
								} ?>
							</p>
							<a data-buy_now data-nyop="<?php echo (string) $nyop; ?>" data-price="<?php echo (string) $price; ?>" href="#" class="blue btn buy-now btn-primary btn-large hidden">Buy Now &raquo;</a>
						</span>
						<div class="text-con"><?php the_content(); ?></div>
					</section>
				</article>
				<?php endwhile; ?>
			</section>
		<!--!end content inner-->
		</div>
		<section id="store-top">
			<h3>Store <span class="hide-medium hide-touch">&raquo;</span> <span class="title hide-medium hide-touch"><?php the_title(); ?></span></h3>
			<div class="cart-preview">
				<span class="text-con">
					<span class="ifont ifontcart"></span> <span class="preview-text">Loading Cart&hellip;</span>
				</span>
				<a href="#" class="btn btn-success hidden" data-checkout_theater>Checkout</a>
			</div>
		</section>
		<div class="loading"></div>
	<!--! end content outer-->
	</div>
	<script type="text/x-handlebars-template" id="shopping-cart-html">
	<div id="shopping-cart" data-base_shipping=<?php echo get_option('riot_shipping_domestic'); ?> data-intl_shipping=<?php echo get_option('riot_shipping_intl'); ?>>
		<div class="bottom-updater gray-texture">
			<span class="ifont">s</span>
			<div class="fltlt">$</div>
			<a href="<?php echo site_url('checkout'); ?>" class="btn fltrt btn-primary" data-checkout_theater> <?php _e('View Cart / Checkout &raquo;'); ?></a>
		</div>
		<div class="add-to-cart">
			<div class="add-to-cart-inner-frame">
				<div class="add-title wood align-center"><?php _e('Add to Cart') ?></div>
				<div class="nyop-info">
					<h2>Why Name Your Own Price?</h2>
					<p>It's not about the money for us. It's about making great movies that glorify God and bless people.</p>
					<p>We don't want a certain price tag to keep people from seeing our films, so we let you pay whatever the heck you want. At the end of the day, we get to be thankful for God's provision and people's generosity.</p>
					<p>We hope you'll still pay whatever you to support what we do.</p>
				</div>
				<div class="add-to-cart-inner">
					<h2></h2>
					<form>
						<label for="qty"><?php _e('Qty'); ?></label>
						<input name="qty" type="number" /><br>
						<label for="price"><?php _e('Price'); ?></label>
						<input name="price" type="number" step="any" />
					</form>
					<div class="btn-con">
						<a href="#" data-close class="back btn btn-warning">&laquo; Keep Shopping</button><a href="#" data-close class="back btn btn-danger">&laquo; Cancel</button><a href="<?php echo site_url('checkout'); ?>" data-checkout_theater class="forward btn btn-success"><?php _e('Cart / Checkout &raquo;'); ?></button><a href="#" data-add_to_cart class="forward btn btn-primary">Add to Cart &raquo;</a>
					</div>
				</div>
			</div>
			<div class="riot-modal-background"></div>
		</div>
	</div>
	</script>
	<script id="checkout-theater-html" type="text/x-handlebars-template">
		<div id="checkout-theater" class="gray-texture">
			<div class="frame-outer">
				<div class="frame-inner">
					<div class="form-container"></div>
				</div>
				<div class="loading"></div>
			</div>
			<a href="#" class="close-btn" data-close_checkout>&times;</a>
		</div>
	</script>
<?php if (!$Riot->isAjax()): ?>
	<div class="touch-outset"></div>
<!--!end default content-->
</div>
<?php
get_sidebar();
?>
<?php
get_footer();
endif;
?>