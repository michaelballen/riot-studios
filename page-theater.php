<?php

date_default_timezone_set("US/Central");

global $do_main_header;

//check if watch token was included in request
$watch_token = $_REQUEST['wT'];
$order_id = $_REQUEST['oI'];

if (empty($order_id)) {
	header('Location: ' . site_url('store'));
}

date_default_timezone_set("US/Central");

//lookup the watch token
$order_lookup = get_post_meta($order_id, 'items', true);
$date_created = get_the_time('Y-m-d H:i:s', $order_id);
$date_created_ts = strtotime($date_created);
$date_to_start_ts = strtotime($date_created . ' + 30 days');
$date_to_start = date('F j, Y \a\t h:iA T', $date_to_start_ts);
$items = (array) json_decode($order_lookup);
$has_token = false;

foreach($items as $k=>$v) {
	if (!empty($v->watch_token) && $v->watch_token === $watch_token) {
		$has_token = $v;
		break;
	}
}

if ($has_token === false) {
	header('Location: ' . site_url('store'));
}

$thumb = get_the_post_thumbnail($has_token->id, 'medium-square', array(
	'class'=>'separate'
));
$video_title = get_bloginfo('template_directory') . '/videos/' . $has_token->streaming_video;

//we have an item with that watch token and id, let's see what state the rental is in
$state = '';
$current_date = time();
if (empty($has_token->date_started)) {
	//has it been 30 days?
	if ($date_to_start_ts < $current_date) {
		$state = 'expired';
		$expiration_date = $date_to_start;
	} else {
		$state = 'not_started';
	}
} else {
	$date_started = strtotime($v->date_started);
	$date_expire = strtotime($v->date_started . ' + 3 days');
	$expiration_date = date('F j, Y \a\t h:iA T', $date_expire);
	if ($date_expire < $current_date) {
		$state = 'expired';
	} else if ($date_to_start_ts < $current_date) {
		$state = 'expired';
		$expiration_date = $date_to_start;
	} else {
		$state = 'started';
	}
}

$do_main_header = false;
$module = 'theater';
//wp_enqueue_style('videojs');
get_header('black');
?>
<div class="default-content">
	<div class="content-outer" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
				<h1><?php
				if ($state === 'expired') {
					_e('Rental Expired');
				} else {
					_e($has_token->title);
				}
				?></h1>
				<section class="separate">
					<?php
					if ($state === 'expired') :
						_e('<p>It looks like your link to view &ldquo;' . $has_token->title . '&rdquo; expired ' . $date_to_start . '. It\'s not all bad though. You can still purchase another streaming session for NAME-YOUR-PRICE.</p>');
					elseif ($state === 'not_started') :
						_e('<h2>Ready to Start Watching?</h2>');
						_e('<p>You have not begun playing this rental of &ldquo;' . $has_token->title . '&rdquo; yet. Streaming rentals are available to start watching 30 days from the date purchased. Once the video has been started, you\'ll have 3 days to finish it before it expires.</p>');
						echo '<div class="separate align-center"><a href="#" data-show_rental>' . $thumb . '</a></div>';
					else :
						_e('<h2>Continue Watching?</h2>');
						_e('<p>Looks like you\'ve already started watching &ldquo;' . $has_token->title . '.&rdquo; Once a rental video has been started, your link is active for 3 more days. Your rental is set to expire <strong>' . $expiration_date . '</strong>. Click below to play the movie.</p>');
						echo '<div class="separate align-center"><a href="#" data-show_rental>' . $thumb . '</a></div>';
					endif;
					?>
					
				</section>
				<div class="theater-btn-con group">
				<?php if ($state === 'expired') { ?>
					<a href="<?php echo get_permalink($has_token->id); ?>" class="big btn btn-warning"><?php _e("Rent Again &raquo;"); ?></a>
				<?php } else { ?>
					<a href="#" data-show_rental class="big btn btn-primary">Watch Now &raquo;</a>
				<?php } ?>
					<a href="<?php echo site_url(); ?>" class="big btn">&laquo; Back to Riot Studios</a>
				</div>
		</div>
	</div>
</div>
<div id="rental-player"<?php if (empty($date_started)) : ?> data-mark_as_watched <?php endif; ?> data-watch_token="<?php echo $watch_token; ?>" data-order_id="<?php echo $order_id; ?>" data-video_title="<?php echo $video_title; ?>" data-nonce="<?php echo wp_create_nonce('streamRiotVideoNonce'); ?>">
	<video id="rental-player-video" class="video-js vjs-default-skin" controls preload="auto" width="1280" height="720" data-setup='{"example_option":true}'></video>
	<a href="#" class="close-btn" data-close_rental>&times;</a>
</div>
<?php
get_footer('black');
?>