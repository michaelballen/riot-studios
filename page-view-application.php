<?php
$module = 'job-application';
$wait_for_js = false;
session_start();
//lots to do here...
try {
	$application = $Riot->getJobApplication();
	$_SESSION['job_app_secret'] = $application->secret;
} catch (Exception $e) {
	header('Location: ' . site_url('apply?error=' . urlencode($e->getMessage())));
}
$read_only = !empty($_REQUEST['readonly']);
get_header('black');
?>
<div class="default-content" data-module="<?php echo $module; ?>"></div>
	<div class="content-outer" data-title="<?php $Riot->do_title(); ?>" id="job-application">
		<div class="content-inner">
			<header>
				<div class="left-side">
					<a href="<?php echo site_url(); ?>"><span class="ifontriot-logo"></span></a>
				</div>
				<div class="right-side">
					<h1>Riot Studios</h1>
					<h2 style="padding-left:4px;">Job Application</h2>
					<?php if (!$read_only): ?>
					<p>We'll save your progress every 30 seconds, and you can always use <a href="<?php echo $application->link; ?>">this link</a> to access your app where you left off.</p>
					<p>If something on the website malfunctions, call us at 214.534.3355.</p>
					<?php endif; ?>
				</div>
			</header>
			<form method="post" accept-charset="utf-8" id="job-application-form" <?php if ($read_only) {
				echo 'data-read_only=1';
			} ?>>
			<section>
				<div class="left-side">
					<h2>Basic Info</h2>
				</div>
				<div class="right-side">
					<div class="control-group full-name">
						<label for="user_first_name">Name</label>
						<input type="text" name="user_first_name" placeholder="First" size="12" value="<?php echo $application->user_first_name; ?>">
						<input type="text" name="user_middle_name" placeholder="MI" size="1" value="<?php echo $application->user_middle_name; ?>">
						<input type="text" name="user_last_name" placeholder="Last" size="15" value="<?php echo $application->user_last_name; ?>">
					</div>
					<div class="control-group">
						<label for="user_email">Email</label>
						<input type="email" name="user_email" placeholder="@" value="<?php echo $application->user_email; ?>">
					</div>
					<div class="control-group">
						<label for="user_phone">Phone</label>
						<input type="tel" name="user_phone" placeholder="XXX-XXX-XXXX" value="<?php echo $application->user_phone; ?>">
					</div>
					<div class="control-group full-name">
						<label for="city">Current Location</label>
						<input type="text" name="city" placeholder="City" size="12" value="<?php echo $application->city; ?>">
						<select name="state" class="state-select">
							<?php
							if (empty($application->state)) {
								$Riot->doStateOptions('TX');
							} else {
								$Riot->doStateOptions($application->state);
							}
							?>
						</select>
					</div>
					<div class="group">
						<div class="control-group social-media-input">
							<label for="user_fb_profile"><span class="ifontfacebook facebook"></span></label>
							<input id="facebook-input" type="text" name="user_fb_profile" value="<?php echo $application->user_fb_profile; ?>" placeholder="Profile URL">
						</div>
						<div class="control-group social-media-input">
							<label for="user_twitter_profile"><span class="ifonttwitter twitter"></span></label>
							<input id="twitter-input" type="text" name="user_twitter_profile" value="<?php echo $application->user_twitter_profile; ?>" placeholder="@yourtwittername">
						</div>
						<p class="note" style="font-size:12px;"><span style="font-weight:bold">Sidenote:</span> <a href="http://facebook.com/riotstudios">Liking</a> and <a href="http://twitter.com/riotstudios">Following</a> us and our projects gets you bonus points&hellip;</p>
					</div>
				</div>
			</section>
			
			<section>
				<div class="left-side">
					<h2>Position</h2>
				</div>
				<div class="right-side">
					<div class="control-group apply-positions">
						<label for="user_first_name">Please check the box for one or more positions that interest you:</label>
						<?php
						$jobs_qry = new WP_Query(array(
							'post_type'=>'employment_opening',
							'posts_per_page'=>0
						));
						if ($jobs_qry->have_posts()) :
						?>
						<ul class="positions-list" id="positions-list">
						<?php
							while($jobs_qry->have_posts()) : $jobs_qry->the_post();
								?>
									<li>
										<div class="title"><a class="box<?php if ($application->job_interest[$post->ID] == 1) {
											echo ' checked';
										} ?>" href="#"><span class="ifontcheckmark"></span></a><?php the_title(); ?></div>
										<a class="toggle-button" href="#apply-position-<?php echo $post->ID; ?>" data-toggle_section>
											<?php $Riot->do_three_lines(); ?>
										</a>
										<article class="body" id="apply-position-<?php echo $post->ID; ?>"><?php the_content(); ?></article>
										<input type="hidden" name="job_interest[<?php echo $post->ID; ?>]" value="<?php
										if ($application->job_interest[$post->ID] == 1) {
											echo '1';
										} else {
											echo '0';
										}
										?>">
									</li>
								<?php
							endwhile;
						?>
						</ul>
						<?php
						endif;
						?>
					</div>
				</div>
			</section>
			
			<section>
				<div class="left-side">
					<h2>Education</h2>
				</div>
				<div class="right-side">
					<div class="control-group">
						<label for="education_acquired">Acquired Education</label>
						<select name="education_acquired" id="education-acquired">
							<?php
							$edu_arr = array(
								'',
								'No College',
								'Some College',
								'Undergrad Degree',
								'Masters Degree'
							);
							foreach($edu_arr as $v) :
							?>
								<option value="<?php echo $v; ?>"<?php if ($application->education_acquired == $v) {
									echo ' selected';
								} ?>><?php echo $v; ?></option>
							<?php
							endforeach;
							?>
						</select>
					</div>
					
					<div class="control-group<?php
					if ($application->education_acquired === 'No College' || $application->education_acquired === '') {
						echo ' hidden';
					}
					?>" id="undergrad-input-group">
						<label for="college"><?php
						if ($application->education_acquired === 'Some College') {
							_e('Currently Attending');
						} else {
							_e('Earned Undergrad from');
						}
						?></label>
						<input type="text" name="college" value="<?php echo $application->college; ?>" size="40" placeholder="Name of Your School for Undergrad">
						<label for="college_degree"><?php
						if ($application->education_acquired === 'Some College') {
							_e('Studying');
						} else {
							_e('In');
						}
						?></label>
						<input type="text" name="college_degree" value="<?php echo $application->college_degree; ?>" size="40" placeholder="Your Major">
					</div>
					<div class="control-group masters<?php
					if ($application->education_acquired !== 'Masters Degree') {
						echo ' hidden';
					}
					?>" id="masters-input-group">
						<label for="masters">Earned Masters from</label>
						<input type="text" name="masters" value="<?php echo $application->masters; ?>" size="40" placeholder="Name of Your School for Masters">
						<label for="masters_degree">In</label>
						<input type="text" name="masters_degree" value="<?php echo $application->masters_degree; ?>" size="40" placeholder="Type of Degree You Received">
					</div>
					
				</div>
			</section>
			
			<section>
				<div class="left-side">
					<h2>References</h2>
				</div>
				<div class="right-side">
					<div class="control-group">
						<label>Please provide 2 references</label>
					</div>
					<ul class="reference-list" id="reference-list">
						<?php
						$ref_i = 0;
						while ($ref_i < 2) :
						?>
						<li>
							<div class="content">
								<div class="group">
									<div class="control-group name">
										<label for="reference_<?php echo (string) $ref_i; ?>_name">Name</label>
										<input type="text" name="references[<?php echo $ref_i; ?>][name]" value="<?php
										if (!empty($application->references[$ref_i]) && !empty($application->references[$ref_i]['name'])) {
											echo $application->references[$ref_i]['name'];
										}
										?>" placeholder="Reference #<?php echo (string) ($ref_i + 1); ?> Name">
									</div>
									<div class="control-group phone">
										<label for="reference_<?php echo (string) $ref_i; ?>_phone">Phone</label>
										<input type="tel" name="references[<?php echo (string) $ref_i; ?>][phone]" value="<?php
										if (!empty($application->references[$ref_i]) && !empty($application->references[$ref_i]['phone'])) {
											echo $application->references[$ref_i]['phone'];
										}
										?>" placeholder="XXX-XXX-XXXX">
									</div>
								</div>
								<div class="control-group">
									<label for="references[<?php echo (string) $ref_i; ?>][rel]">Relationship</label>
									<input type="text" name="references[<?php echo (string) $ref_i; ?>][rel]" value="<?php
									if (!empty($application->references[$ref_i]) && !empty($application->references[$ref_i]['rel'])) {
										echo $application->references[$ref_i]['rel'];
									}
									?>">
								</div>
							</div>
							<div class="number-display"><span><?php echo (string) $ref_i + 1; ?></span></div>
						</li>
						<?php
							$ref_i += 1;
						endwhile;
						?>
					</ul>
				</div>
			</section>
			<section>
				<div class="left-side">
					<h2>Q&amp;A</h2>
				</div>
				<div class="right-side">
					<div class="control-group">
						<label>What would you be most excited to learn or experience doing this job?</label>
						<textarea name="what_to_learn" cols="30" rows="6"><?php echo str_replace("<br>", "\n", $application->what_to_learn); ?></textarea>
					</div>
					<div class="control-group">
						<label>After the summer is over, what quality or characteristic do you want people to remember you for?</label>
						<textarea name="how_to_remember" cols="30" rows="6"><?php echo str_replace("<br>", "\n", $application->how_to_remember); ?></textarea>
					</div>
					<div class="control-group">
						<label>What's the worst movie ever seen and why?</label>
						<textarea name="worst_movie" cols="30" rows="6"><?php echo str_replace("<br>", "\n", $application->worst_movie); ?></textarea>
					</div>
				</div>
			</section>
			
			<section>
				<div class="left-side">
					<h2>R&egrave;sum&egrave;</h2>
				</div>
				<div class="right-side">
					<div class="control-group">
						<?php if (!$read_only) : ?>
						<label>Please upload a PDF (email Word Docs to ryan@riotstudios.com)</label>
						<button tabindex="-1" type="button" class="btn resume-input <?php
						if (empty($application->resume)) {
							echo 'btn-primary';
						} else {
							echo 'btn-success';
						}
						?>"><span class="full"><span class="ifontattachment"></span> File Uploaded. Click to Upload a Different File.</span><span class="load">Uploading &hellip;</span><span class="empty">Click to Upload R&egrave;sum&egrave;</span><input id="resume-input" name="resume_upload" type="file" accept="application/doc,application/pdf" /></button>
						<?php
						endif;
						if (!empty($application->resume) && $read_only) {
						?>
							<a href="<?php echo $application->resume; ?>" class="btn" target="_blank">Click to view resume</a>
						<?php
						}
						?>
						<input type="hidden" name="resume" value="<?php echo $application->resume; ?>">
						<iframe style="display:none; width:0; height:0;" name="resume_upload_target" id="resume_upload_target"></iframe>
						
						<p class="small" style="margin-top:0.5em; font-style:italic;">If you have trouble uploading, email us with the same email listed here to ryan@riotstudios.com, with &ldquo;R&egrave;sum&egrave;&rdquo; and your name as the subject line".</p>
					</div>
				</div>
			</section>
			
			<section class="final-section">
				<?php if (!$read_only) : ?>
				<p>Thanks for applying.</p>
				<p>Double-check your application for errors, then click "submit." Once we receive your app, we will contact you with any necessary follow-up. We won't share your personal information with anyone else.</p>
				<p>Whether or not you land this position, we really appreciate you and hope you get to use your God-given gifts somewhere great.</p>
				<input type="hidden" name="status" value="<?php echo $application->status; ?>">
				
				<button type="button" class="btn <?php
				if ($application->status === 'complete') {
					echo 'btn-success';
				} else {
					echo 'btn-primary';
				}
				?> submit-btn" id="application-submit"><span class="primary">Submit <span class="ifontarrow-right"></span></span><span class="progress">Submitting &hellip;</span><span class="success">Your App Has Been Submitted!</span></button>
				<?php endif;//end if read only ?>
			</section>
			<?php wp_nonce_field('riotJobApplication', 'job_app_nonce'); ?>
			<input type="hidden" name="application_id" value="<?php echo $application->ID; ?>">
			<input type="hidden" name="application_secret" value="<?php echo $application->secret; ?>">
			</form>
		<!--!end content inner-->	
		</div>
		<?php if (!$read_only) : ?>
		<a class="save-container" href="#" id="save-container">
			<span class="ifontdisk save"></span>
			<span class="saving">Saving&hellip;</span>
			<span class="saved"><span class="ifontcheckmark"></span> Saved</span>
			<span class="error"><span>&times;</span> Error</span>
		</a>
		<?php endif; ?>
	<!--! end content outer-->
	</div>
<?php
get_footer('black');
?>