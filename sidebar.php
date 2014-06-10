<?php
global $Riot;
?>
<div id="desktop-socializer" role="complementary">
	<div class="container">
		<div class="top-container news">
			<div class="top-dial">
				<a href="#" data-feed_class="news" class="feed-button news"><span class="ifontbubble"></span></a>
				<a href="#" data-feed_class="friends" class="feed-button friends"><span class="ifontuser"></span></a>
				<div class="tick-line"></div>
			</div>
			<div class="feed-area">
				<div class="feed-inner"></div>
				<div class="top-image"></div>
				<div class="feed-content">
					<div id="twitter-feed" data-max_pages="<?php echo $Riot->twitter->getMaxPages(); ?>">
						<?php $Riot->twitter->showRecentTweets(); ?>
						<button class="btn more-tweets" type="button" data-load_tweets><span class="default">More Tweets&hellip;</span><span class="load-text"><div class="three-loading-dots"><span></span><span></span><span></span></div></span></button>
					</div>
					<div id="socializer-friends">
						<div id="twitter-followers" class="group">
							<?php $Riot->twitter->showFollowers(); ?>
						</div>
						<div id="socializer-join-btns">
							<a class="btn btn-primary" id="socializer-join" href="#join-modal">Join the Riot</a>
							<div id="join-modal" class="hidden" data-modal data-top=250>
								<div class="group join-modal">
									<h2>Join the Riot!</h2>
									<a href="http://twitter.com/intent/user?screen_name=riotstudios" target="_blank" class="btn twitter"><span class="ifonttwitter ifont"></span> Follow @riotstudios</a>
									<a href="http://facebook.com/riotstudios" target="_blank" class="btn facebook"><span class="ifont ifontfacebook"></span> Like Riot on Facebook</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom-container twitter" data-type="twitter">
			<div class="bottom-dial">
				<div class="ico-container">
					<span class="ifonttwitter"></span>
					<span class="ifontfacebook"></span>
					<span class="ifontmail"></span>
				</div>
				<a href="#social-tweet" data-tick_class="twitter" class="input-selector twitter"></a>
				<a href="#social-fbpost" data-tick_class="facebook" class="input-selector facebook"></a>
				<a href="#social-email" data-tick_class="email" class="input-selector email"></a>
				<div class="measure-line"></div>
			</div>
			<div class="input-area">
				<div class="input-inner"></div>
				<div class="bottom-image"></div>
				<div class="user-email-wrapper">
					<input id="social_user_email" name="social_user_email" placeholder="Your Email">
				</div>
				
				<textarea name="social_user_post" id="social_user_post" placeholder="Your tweet..."></textarea>
				<?php wp_nonce_field('contactForm', 'contact_nonce'); ?>
				<div class="button-wrapper">
					<button type="button" class="btn btn-primary" id="social_user_submit"><span class="btn-text">Tweet</span></button>
				</div>
				
			</div>
		</div>
	</div>
	<div id="socializer-modal" class="hidden"><div style="width:320px;"><h2></h2><p></p></div></div>
	<a class="handle rotated wood" href="#desktop-socializer" data-toggle_desktop_social>
		<?php $Riot->do_three_lines(); ?>
	</a>
</div>

<a id="mobile-social-btn" href="#" class="pointer show-mobile" data-sm_mobile_toggle>
	<?php $Riot->do_three_lines(); ?>
</a>

<div id="mobile-social" class="wood">
	<div class="group">
		<div class="twitter">
			<span><a target="_blank" href="<?= $Riot->twitter->getTweetURL(); ?>" class="btn">Tweet</a></span>
			<span class="ifonttwitter"></span>
			<span><a target="_blank" href="<?= $Riot->twitter->getFollowURL(); ?>" class="btn">Follow</a></span>
		</div>
		<div class="facebook">
			<span><a target="_blank" href="<?= $Riot->facebook->getShareURL(); ?>" class="btn">Share</a></span>
			<span class="ifontfacebook"></span>
			<span><a target="_blank" href="http://facebook.com/riotstudios" class="btn">Like</a></span>
		</div>
		<div class="register register">
			<a data-ajax_load href="<?php echo site_url('register'); ?>" class="btn"><span class="ifontmail"> </span>Email Register</a>
		</div>
	</div>
	<div class="full-btn-con">
		<a href="#" data-sm_mobile_toggle><?php $Riot->do_three_lines(); ?></a>
	</div>
</div>