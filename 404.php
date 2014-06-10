<?php

$module = "single";

if (!$Riot->isAjax()) :
wp_enqueue_script('about-page');
get_header();
?>
<div class="default-content">
<?php endif; ?>
	<div class="content-outer not-found" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
			<h1><?php _e("Not Found"); ?></h1>
			<p class="show-mobile"><?php _e("There's no content here. Try navigating using the icons at the bottom of the page."); ?></p>
			<p class="hide-mobile"><?php _e("There's no content here. Try navigating using the icons to the left."); ?></p>
		<!--!end content inner-->	
		</div>
	<!--! end content outer-->
	</div>
<?php
if (!$Riot->isAjax()):
?>
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