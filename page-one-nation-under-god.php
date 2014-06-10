<?php

$module = "single";
$Riot->page_description = "One Nation Under God, the first documentary from Riot, tells the story of four college guys' cross-country road trip to question their faith";


if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">
<?php
endif;//ajax if stmt
?>
	<div class="content-outer movie-page one-nation-under-god" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
			<div class="content">
				<header>
					<h1><img src="<?php bloginfo('template_directory'); ?>/img/onug-title-block.png" alt="<?php _e('One Nation Under God'); ?>"></h1>
					<div class="left-side">
						<a href="<?php echo site_url('store/one-nation-under-god'); ?>" data-ajax_load><img alt="<?php _e('One Nation Under God'); ?>" src="<?php bloginfo('template_directory'); ?>/img/onug-dvd-with-shadow.png"></a>
					</div>
					<div class="right-side">
						<div class="trailer-container">
							<iframe width="560" height="315" src="http://www.youtube.com/embed/GWVw7cD6KgE" frameborder="0" allowfullscreen></iframe>
						</div>
						<div class="btn-container">
							<a data-ajax_load href="<?php echo site_url('store/one-nation-under'); ?>" class="btn btn-primary">Buy the DVD</a><a data-ajax_load href="<?php echo site_url('store/one-nation-under-god-streaming'); ?>" class="btn btn-success">Stream it Online</a>
						</div>
						<div class="social-media">
							<div class="fb-like" data-href="http://facebook.com/onenationmovie" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true" data-font="lucida grande" data-colorscheme="dark"></div>
							<a href="https://twitter.com/share" class="twitter-share-button" data-text="Riot Studios presents One Nation Under God" data-via="riotstudios" data-related="riotstudios">Tweet</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
						</div>
					</div>
				</header>
				<section class="about">
					<h2><span class="plain-text">About / Film Synopsis</span><img src="<?php bloginfo('template_directory'); ?>/img/boc-about-header.png" alt="About / Film Synopsis"></h2>
					<article>	
<p>It's time for a college road trip like you’ve never seen before.  Michael Allen, Austin Meek, Will Bakke, and Lawson Hopkins have grown up with all the right answers about God, but have yet to really ask any questions.</p>
<p>With a free summer, a working car, and a camera in hand, they’ll travel the country asking, &lquo;Who is God? Where is He? and How is He influencing America?&rquo;</p>
<p>In this hilarious and thought-provoking journey, they’ll find refuge with Mormons, Muslims, atheists, and hippies as they question everything in order to live for something.</p>
<div style="text-align:center; margin:4em 0 0;"><img src="<?php bloginfo('template_directory'); ?>/img/onug-review.jpg" alt="Four Stars for One Nation Under God" /></div>
					</article>
				</section>
				<section>
					<h2><span class="plain-text">Events</span><img src="<?php bloginfo('template_directory'); ?>/img/boc-events-header.png" alt="Media"></h2>
					<article>
						<a href="<?php echo site_url('request-an-event'); ?>">
						<img src="<?php bloginfo('template_directory'); ?>/img/boc-book-a-show.jpg" alt="Book a One Nation Under God Screening">
					</article>
					<article>
						<a href="http://www.providentfilms.org/movieevents/onenationundergod" target="_blank">
							<img src="<?php bloginfo('template_directory'); ?>/img/onug-licensing.jpg" alt="Order a One Nation Under God License">
						</a>
					</article>
				</section>
				<section class="copyright">
					<p>&copy; 2012 Riot Studios L.L.C. All Rights Reserved.<br>&copy; 2011 Provident Films, a unit of SONY MUSIC ENTERTAINMENT. All Rights Reserved.</p>
				</section>
			</div>
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