<?php
session_start();
$module = "apply-page";
$Riot->page_description = "Apply to work at Riot! - positions available now on set or in the office";
if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">
<?php
endif;//ajax if stmt
?>
	<div class="content-outer has-form" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner" id="apply-inner-content">
			<div class="white-container group">
				<header>
					<h1 class="open-sans">I Guess You Can Call It Work&hellip;</h1>
					<h2 class="open-sans">Help Wanted. Inquire Within.</h2>
					<section>
						<?php
						if (have_posts()) :
							while(have_posts()) : the_post();
								the_content();
							endwhile;
						endif;
						?>
					</section>
				</header>
				<article id="apply-positions-container">
					<a class="btn show-mobile" data-toggle_section href="#apply-positions">Available Positions <?php $Riot->do_three_lines(); ?></a>
					<div class="positions" id="apply-positions">
						<h2 class="hide-mobile open-sans">Available Positions</h2>
						<?php
						$jobs_qry = new WP_Query(array(
							'post_type'=>'employment_opening',
							'posts_per_page'=>-1
						));
						if ($jobs_qry->have_posts()) :
							while($jobs_qry->have_posts()) : $jobs_qry->the_post();
								?>
									<section>
										<h3 class="open-sans"><?php the_title(); ?></h3>
										<p><?php the_content(); ?></p>
									</section>
								<?php
							endwhile;
						endif;
						?>
					</div>
				</article>
				<article id="apply-get-started">
					<h2 class="open-sans">Get Started</h2>
					<?php if (!empty($_REQUEST['error'])) : ?>
					<h3 class="error open-sans"><?php echo urldecode($_REQUEST['error']); ?></h3>
					<?php endif; ?>
					<form action="<?php echo site_url('view-application'); ?>" id="apply-form">
						<label for="user_email">Email</label>
						<input placeholder="Email" name="user_email">
						<?php wp_nonce_field('createApplication', 'create_app_nonce'); ?>
						<?php if (!empty($_SESSION['job_app_secret'])) : ?>
							<input type="hidden" name="secret" value="<?php echo $_SESSION['job_app_secret']; ?>">
						<?php endif; ?>
						<button type="submit" class="btn btn-primary">View Application <span class="ifontarrow-right fltrt"></span></a>
					</form>
				</article>
			</div>
		<!--!end content inner-->	
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