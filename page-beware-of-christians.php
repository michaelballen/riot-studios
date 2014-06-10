<?php

$module = "single";
$Riot->page_description = "Beware of Christians, the second documentary from Riot Studios, tells the story of a backpacking trip across Europe to leave religion and follow Jesus";
if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">
<?php
endif;//ajax if stmt
?>
	<div class="content-outer movie-page beware-of-christians" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner auto-scroll-container">
			<div class="content">
				<header class="group">
					<h1><img src="<?php bloginfo('template_directory'); ?>/img/boc-title-block.png" alt="<?php _e('Beware of Christians'); ?>"></h1>
					<div class="left-side">
						<a href="<?php echo site_url('store/beware-of-christians'); ?>" data-ajax_load><img alt="<?php _e('Beware of Christians'); ?>" src="<?php bloginfo('template_directory'); ?>/img/boc-dvd-with-shadow.png"></a>
						<nav>
							<ul data-auto_scroll>
								<li><a href="#boc-about">About</a></li>
								<li><a href="#boc-media">Media</a></li>
								<li><a href="#boc-events">Events</a></li>
							</ul>
						</nav>
					</div>
					<div class="right-side">
						<div class="trailer-container">
							<iframe width="560" height="315" src="http://www.youtube.com/embed/IMIydiF69mA" frameborder="0" allowfullscreen></iframe>
						</div>
						<div class="btn-container">
							<a data-ajax_load href="<?php echo site_url('store/beware-of-christians'); ?>" class="btn btn-primary">Buy the DVD</a><a data-ajax_load href="<?php echo site_url('store/beware-of-christians-streaming'); ?>" class="btn btn-success">Stream it Online</a>
						</div>
						<div class="social-media">
							<div class="fb-like" data-href="http://facebook.com/bocmovie" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true" data-font="lucida grande" data-colorscheme="dark"></div>
							<a href="https://twitter.com/share" class="twitter-share-button" data-text="Beware of Christians!" data-via="riotstudios" data-related="riotstudios">Tweet</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
						</div>
					</div>
				</header>
				<section class="about" id="boc-about">
					<h2><span class="plain-text">About / Film Synopsis</span><img src="<?php bloginfo('template_directory'); ?>/img/boc-about-header.png" alt="About / Film Synopsis"></h2>
					<article>
						<p>Four college students leave their routine Christian lives in the U.S. in a quest to find what it really means to be a follower of Jesus. Alex, Matt, Michael, and Will have grown up as Bible-believing Christians who did all the right things. As they’ve grown older, they’ve realized that the Jesus in the Bible doesn’t exactly look like the healthy, wealthy American Jesus they’ve been trained to know and love.</p> 

						<p>Their journey across Europe, often a comedy of errors, includes a lost passport, an encounter with an Austrian pop star, a surprise discovery of a nude beach, and a romantic postcard entanglement. More importantly, the four guys capture real, honest discussions about what it means to follow Jesus.</p>
					</article>			
				</section>
				
				<section class="group" id="boc-media">
					<h2><span class="plain-text">Media</span><img src="<?php bloginfo('template_directory'); ?>/img/boc-media-header.png" alt="Media"></h2>
					<div class="video-container">
						<h3><?php _e('Beware of Christians Trailer'); ?></h3>
						<a href="http://youtube.com/watch?v=IMIydiF69mA" target="_blank" data-play_video="IMIydiF69mA">
							<img src="<?php bloginfo('template_directory'); ?>/img/boc-video1.jpg" alt="Beware of Christians Trailer">
						</a>
					</div>
					<div class="video-container">
						<h3><?php _e('What is Beware of Christians?'); ?></h3>
						<a href="http://youtube.com/watch?v=zIjP4cbp72c" target="_blank" data-play_video="zIjP4cbp72c">
							<img src="<?php bloginfo('template_directory'); ?>/img/boc-video2.jpg" alt="Video - What is Beware of Christians?">
						</a>
					</div>
					<div class="video-container">
						<h3><?php _e('What has the Response Been?'); ?></h3>
						<a href="http://youtube.com/watch?v=mj4Jx7LMrAA" target="_blank" data-play_video="mj4Jx7LMrAA">
							<img src="<?php bloginfo('template_directory'); ?>/img/boc-video3.jpg" alt="Video - Beware of Christians Responses">
						</a>
					</div>
				</section>
				
				<section class="hide-mobile">
					<h2><span class="plain-text">2011-2012 Tour</span><img src="<?php bloginfo('template_directory'); ?>/img/boc-tour-header.png" alt="Media"></h2>
					<article>
						<img src="<?php bloginfo('template_directory'); ?>/img/boc-tour-schedule.jpg" alt="Beware of Christians Tour Schedule">
					</article>
				</section>
				
				<section id="boc-events">
					<h2><span class="plain-text">Events</span><img src="<?php bloginfo('template_directory'); ?>/img/boc-events-header.png" alt="Media"></h2>
					<article>
						<a href="<?php echo site_url('request-an-event'); ?>">
						<img src="<?php bloginfo('template_directory'); ?>/img/boc-book-a-show.jpg" alt="Book a Beware of Christians Screening">
					</article>
					<article>
						<a href="http://www.providentfilms.org/movieevents/bewareofchristians" target="_blank">
							<img src="<?php bloginfo('template_directory'); ?>/img/boc-licensing.jpg" alt="Order a Beware of Christians License">
						</a>
					</article>
				</section>
				<section class="copyright">
					<h3>Members of the media can visit <a href="http://www.lovell-fairchild.com/pressroom/bewareofchristians">The Press Room</a></h3>
					<img src="<?php bloginfo('template_directory'); ?>/img/boc-header-icons.png" />
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