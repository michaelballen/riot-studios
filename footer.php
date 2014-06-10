<?php global $module, $Riot; ?>
	<div id="desktop-footer">
		<div class="relative group hide-mobile wood-footer">
			<?php wp_nav_menu(array(
				'menu' => 'Desktop Footer',
				'container'=>false,
				'menu_class' => 'footer-menu',
				'theme_location'=>'Footer'
			)); ?>
			<?php
			if (is_user_logged_in()) {
				//show admin menu with stuff like "logout"
			}
			?>
			<a href="#desktop-footer" data-toggle_popout_footer class="toggle-footer">
				<?php $Riot->do_three_lines(); ?>
			</a>
		</div>
		<div class="footer-bottom hide-mobile">
			<div class="group">
				<section>
					<?php echo wpautop(get_option('riot_description')); ?>
					<a href="<?php echo site_url('about'); ?>" class="btn more-btn btn-primary"><?php _e('More about Riot &raquo;'); ?></a>
				</section>
				<section>
					<h3>Stay in Touch</h3>
					<div class="contact-group">
						<a href="http://facebook.com/riotstudios" target="_blank"><span class="ifontfacebook"></span> facebook.com/riotstudios</a>
					</div>
					<div class="contact-group">
						<a href="http://twitter.com/riotstudios" target="_blank"><span class="ifonttwitter"></span> @riotstudios</a>
					</div>
					<div class="contact-group">
						<a href="http://youtube.com/riotfilmhouse" target="_blank"><span class="ifontplay youtube"></span> youtube.com/riotfilmhouse</a>
					</div>
					<div class="contact-group">
						<a href="tel:5126939748"><span class="ifontphone"></span> 512.693.9748</a>
					</div>
					
					<form action="#" data-contact_form data-register_only>
						<div class="control-group half first no-label">
							<input style="padding:8px;" placeholder="Email" name="user_email">
						</div>
						<div class="control-group half">
							<button type="button" class="btn btn-success" data-submit><?php _e('<span class="ifontmail show-huge"></span><span class="show-huge" style="width:6px;"> </span>Register'); ?></button>
						</div>
					</form>
					
				</section>
				<section>
					<h3>Send Us a Note</h3>
					<form data-contact_form>
						<div class="group form-row">
							<div class="control-group half first">
								<label for="user_name">Name</label>
								<input name="user_name" type="text">
							</div>
							<div class="control-group half">
								<label for="user_email">Email*</label>
								<input name="user_email" type="email">
							</div>
						</div>
						<div class="control-group full">
							<label for="user_message">Message*</label>
							<textarea name="user_message" rows="2"></textarea>
						</div>
						<div class="control-group">
							<label for="user_register" class="checkbox">
								<input type="checkbox" name="user_email_register" value="1" checked>I want to get email updates from Riot
							</label>
						</div>
						<?php wp_nonce_field('contactForm', 'contact_nonce'); ?>
						<button type="button" class="btn btn-primary" data-submit>Submit &raquo;</button>
					</form>
				</section>
			</div>
		</div>
	</div>
<!--! end wrapper div-->
</div>
<div id="article-theater"<?php
if ($wp_query->post->post_type === 'article' && $wp_query->is_single) {
	echo ' class="on shown"';
	echo ' data-preload="' . $wp_query->post->ID . '"';
}
?>>
	<article class="scrollable">
		<div class="group scroll-target">
			<?php
			if ($wp_query->post->post_type === 'article' && $wp_query->is_single) :
				while($wp_query->have_posts()) : $wp_query->the_post();
			?>
			<div class="article-container">
				<header class="group">
					<div class="post-date">
						<span><?php the_date('F j, Y'); ?></span>
					</div>
					<h1><? the_title(); ?></h1>
					<div class="author-con align-center">
						<span class="author-thumb">
							<?php echo get_avatar( get_the_author_meta('ID'), 'thumbnail'); ?>
						</span>
						<h2>by <?= get_the_author(); ?></h2>
					</div>
				</header>
				<section role="main"><?php the_content(); ?></section>
			</div>
			<?php
				endwhile;
			else :
			?>
				<div class="article-container">
					<header class="group">
						<div class="post-date">
							<span></span>
						</div>
						<h1></h1>
						<div class="author-con align-center">
							<span class="author-thumb"></span>
							<h2>by </h2>
						</div>
					</header>
					<section></section>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<a data-article_theater_close class="theater-close" href="#">
		<span class="x">&times;</span>
		<span class="text"><?php _e('&laquo; Close Article'); ?></span>
	</a>
</div>
<div id="video-theater"<?php
if ($wp_query->post->post_type === 'video' && $wp_query->is_single) {
	echo ' class="on shown"';
	echo ' data-preload="' . get_post_meta($wp_query->post->ID, '_vidid', true) . '"';
	echo ' role="main"';
}
?>>
	<a data-close_video class="theater-close" href="#">
		<span class="x">&times;</span>
		<span class="text"><?php _e('&laquo; Close Video Theater'); ?></span>
	</a>
	<div class="video-con"><div id="video-player"></div></div>
</div>
<div id="image-theater"<?php
if ($wp_query->post->post_type === 'image' && $wp_query->is_single) {
	echo ' class="on shown"';
	echo ' data-preload="' . $wp_query->post->ID . '"';
	echo ' role="main"';
}
?>>
	<a data-image_theater_close class="theater-close" href="#">
		<span class="x">&times;</span>
		<span class="text"><?php _e('&laquo; Close Image Theater'); ?></span>
	</a>
	<div class="image-slideshow">
		<ul></ul>
	</div>
	
	<a href="#" data-image_thumbs_prev class="thumb-arrow"><span class="ifontarrow-left"></span></a>
	<a href="#" data-image_thumbs_next class="thumb-arrow right"><span class="ifontarrow-right"></span></a>
	<a href="#" data-image_full_prev class="arrow"><span class="ifontarrow-left"></span></a>
	<a href="#" data-image_full_next class="arrow right"><span class="ifontarrow-right"></span></a>
	<div class="progress-bullets"></div>
	<div class="info-con">
		<a href="#" class="close-btn" data-image_theater_close_info>&times;</a>
		<div class="side-menu hide-mobile">
			<ul>
				<li class="info"><span class="tooltip">Read Info</span><a href="#" data-image_theater_caption><span class="ifontinfo"></span></a></li>
				<li class="thumb"><span class="tooltip">Thumbnails</span><a href="#" data-image_theater_thumbs><span class="ifontimage"></span></a></li>
				<li><span class="tooltip">Share</span><a data-socializer_focus="facebook" href="#"><span class="ifontfacebook"></span></a></li>
				<li><span class="tooltip">Tweet</span><a id="image-slideshow-twitter-share" href="https://twitter.com/intent/tweet"><span class="ifonttwitter"></span></a></li>
			</ul>
		</div>
		<div class="image-thumbs">
			<ul class="group"></ul>
			<span class="ifontimage"></span>
		</div>
		<div class="image-caption">
		</div>
	</div>
</div>
<div id="page-loading"></div>
<script>
    var _gaq=[['_setAccount','UA-26882346-1'],['_trackPageview']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
	g.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<?php wp_footer(); ?>
<script src="<?php bloginfo('template_directory'); ?>/js/require-jquery.js" data-main="<?php bloginfo('template_directory'); ?>/js/<?php echo $module; ?>"></script>
</body>
</html>