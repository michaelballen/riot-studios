<?php

$module = "about";

if (!$Riot->isAjax()) :
wp_enqueue_script('about-page');
get_header();
?>
<div class="default-content">
<?php endif; ?>
	<div class="content-outer" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner padded">
			<?php
			if (have_posts()) :
				while(have_posts()) : the_post();
				$id = get_the_ID();
			?>
				<article id="team-member-<?php echo $id; ?>">
					<h1><?php the_title(); ?></h1>
					<h2><?php echo get_post_meta($id, 'position', true); ?></h2>
					<?php the_content(); ?>
				</article>
			<?php	
				endwhile;			
			endif;
			?>
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