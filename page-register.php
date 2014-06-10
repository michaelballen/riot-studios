<?php
$module = 'form';
if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">
<?php
endif;//ajax if stmt
?>
	<div class="content-outer has-form" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
			
			<div class="white-container">
				<h1>Stay in Touch!</h1>
				
				<form action="<?php echo site_url('api'); ?>" data-ajax_form data-success_ui="register-success-hb">
				
					<label for="user_name">Name</label>
					<input type="text" name="user_name" value="">
				
					<label for="user_email">Email</label>
					<input type="email" name="user_email" value="">
					
					<input type="hidden" name="action" value="registerUser">
				
					<button class="btn btn-primary big" data-submit type="button">Submit &raquo;</button>
				</form>
				
				
				<script type="text/x-handlebars-template" id="register-success-hb">
					<div class="set-apart">
						<h2>Thanks!</h2>
						<p>{{msg}}</p>
					</div>
				</script>
				
				<a target="_blank" href="<?= $Riot->twitter->getFollowURL(); ?>" class="btn twitter big margin">Follow on Twitter</a>
				
				<a target="_blank" href="http://facebook.com/riotstudios" class="btn facebook big">Like on Facebook</a>
				
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