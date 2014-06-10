<?php
if (isset($_REQUEST['request_id']) && isset($_REQUEST['read_only']) && $_REQUEST['read_only'] == '1') {
	if (!is_user_logged_in()) {
		header('Location: ' . site_url('request-an-event'));
	}
	if (get_post_type($_REQUEST['request_id']) != 'event_request') {
		header('Location: ' . site_url('request-an-event'));
	}
	$request_obj = $Riot->makeMetaObject($_REQUEST['request_id']);
	$read_only = true;
} else {
	$read_only = false;
	$request_obj = new stdClass;
}
$module = 'request-an-event';
$Riot->page_description = 'Bring the Riot guys to your event or screen our movies at your college, church, or school';
get_header('black');
?>
<div class="default-content" data-module="<?php echo $module; ?>"></div>
	<div class="content-outer" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
			<header>
				<div class="group" style="min-height:140px;">
					<div class="left-side">
						<a href="<?php echo site_url(); ?>"><span class="ifontriot-logo"></span></a>
					</div>
					<div class="right-side" style="padding-top:40px;">
						<h1>Riot Studios</h1>
						<h2 style="padding-left:4px;"><?php _e("Screening and Speaking Events"); ?></h2>
					</div>
				</div>
				
				<section class="top-description">
					<?php
					if (have_posts()) :
						while(have_posts()) : the_post();
							the_content();	
						endwhile;
					endif;
					?>
				</section>
				
			</header>
			
			<form method="post" accept-charset="utf-8" id="request-event-form" <?php if ($read_only) {
				echo 'data-read_only=1';
			} ?>>
			
			<section>
				<div class="left-side">
					<h2>Contact Info</h2>
				</div>
				<div class="right-side">
					<div class="control-group full-name">
						<label for="user_name" required>Name</label>
						<input size="40" type="text" name="user_name" value="<?php echo $request_obj->user_name; ?>">
					</div>
					<div class="group two-inputs">
						<div class="control-group">
							<label for="user_email" required>Email</label>
							<input type="email" name="user_email" placeholder="" value="<?php echo $request_obj->user_email; ?>">
						</div>
						<div class="control-group">
							<label for="user_phone">Phone</label>
							<input type="tel" name="user_phone" placeholder="XXX-XXX-XXXX" value="<?php echo $request_obj->user_phone; ?>">
						</div>
					</div>	
					<div class="control-group">
						<label><input type="checkbox" name="email_register"> Get Email Updates from Riot</label>	
					</div>
				</div>
			</section>
			
			<section>
				<div class="left-side">
					<h2>Event Info</h2>
				</div>
				<div class="right-side">
					<div class="control-group">
						<label for="event_location_state">Event Location</label>
						<input type="text" name="event_location_city" placeholder="City" size="12" value="<?php echo $request_obj->event_location_city; ?>">
						<select name="event_location_state" class="state-select">
							<?php
							$Riot->doStateOptions($request_obj->event_location_state);
							?>
						</select>
					</div>
					
					<div class="control-group">
						<label for="event_date">Preferred Date(s)</label>
						<input type="text" name="event_dates" placeholder="MM/DD/YYYY - MM/DD/YYYY" size="24" value="<?php echo $request_obj->event_dates; ?>">
					</div>
					
					<div class="control-group">
						<label for="event_guys">How Many Guys Would You Like to Bring Out? (1-3)</label>
						<input type="text" name="event_guys" value="<?php echo $request_obj->event_guys; ?>">
					</div>
					
					<div class="control-group">
						<label for="event_honorarium">What Honorarium Are You Willing to Provide?</label>
						<input type="text" name="event_honorarium" placeholder="$" value="<?php echo $request_obj->event_honorarium; ?>">
					</div>
					
					<div class="control-group">
						<label for="event_travel_exp">Are you able to cover travel expenses?</label>
						<select name="event_travel_exp">
							<option value=""></option>
							<option value="Yes" <?php if ($request_obj->event_travel_exp == 'Yes') {
								echo 'selected';
							} ?>>Yes</option>
							<option value="No" <?php if ($request_obj->event_travel_exp == 'No') {
								echo 'selected';
							} ?>>No</option>
						</select>
					</div>
					
					<div class="control-group">
						<label for="event_merch">Will You Allow Sales of Merchandise (Name-Your-Price DVDs) at your event?</label>
						<select name="event_merch">
							<option value=""></option>
							<option value="Yes" <?php if ($request_obj->event_merch == 'Yes') {
								echo 'selected';
							} ?>>Yes</option>
							<option value="No" <?php if ($request_obj->event_merch == 'No') {
								echo 'selected';
							} ?>>No</option>
						</select>
					</div>
					
					<div class="control-group">
						<label for="event_description">Please provide a brief description of the event&hellip;</label>
						<textarea name="event_description" id="" rows="3"><?php echo $request_obj->event_description; ?></textarea>
					</div>
					
				</div>
			</section>
			
			<?php if (!$read_only) : ?>
			
			<section class="final-section">
				
				<p>Thanks for your your interest.</p>
				<p>We hope your event works out, and we look forward to speaking with you about it.</p>
				<input type="hidden" name="status" value="<?php echo $application->status; ?>">
				
				<button type="button" class="btn <?php
				if ($application->status === 'complete') {
					echo 'btn-success';
				} else {
					echo 'btn-primary';
				}
				?> submit-btn" id="request-event-submit"><span class="primary">Submit <span class="ifontarrow-right"></span></span><span class="progress">Submitting &hellip;</span><span class="success">Your Request Has Been Submitted!</span></button>
				
			</section>
			<?php wp_nonce_field('requestEvent', 'request_event_nonce'); ?>
			<input type="hidden" name="action" value="requestEvent">
			
			<?php endif;//end if read only ?>
			
			</form>
		<!--!end content inner-->	
		</div>
	<!--! end content outer-->
	</div>
<?php
get_footer('black');
?>