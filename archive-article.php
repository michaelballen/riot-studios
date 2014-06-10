<?php
/*
Template Name: Blog
*/

$module = "blog";//js module
$Riot->page_description = 'Read the latest articles from Riot Studios';

if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content blog">
<?php endif; ?>
	<div class="content-outer blog" id="scrollable" data-module="<?php echo $module; ?>" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner">
		<?php
		$query = new WP_Query(array(
			'post_type' => 'article'
		));
		$class_str = '';
		while ($query->have_posts()) : $query->the_post();//start the loop
		?>
			<article class="group" data-id="<? the_ID(); ?>" data-meta='<?= $Riot->makePostMeta(); ?>' >
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="group" data-article_theater>
					<?php
					if (has_post_thumbnail()) {
						the_post_thumbnail('medium-square', array(
							'class'=>'fltlt article-thumbnail'
						));	
					} else {
					?>
						<img src="<? bloginfo('template_directory') ?>/img/blue-ph.jpg" class="fltlt article-thumbnail" />
					<?php
					}
					$author = get_userdata($post->post_author);
					
					?>
					<div class="date"><span><?php the_time('F j, Y'); ?></span></div>
					<h2><? the_title(); ?></h2>
					<h3 class="author"><? echo get_avatar($author->ID, 'thumbnail'); ?> by <? the_author(); ?></h3>
					<p><?= wp_trim_words(get_the_content(), 30); ?></p>
				</a>
			</article>
		<?php endwhile; //end article loop ?>
		<!--!end content inner-->	
		</div>
	<!--! end content outer-->
	</div>
<?php if (!$Riot->isAjax()): ?>
	<div class="touch-outset"></div>
<!--!end default content-->
</div>
<?php
get_sidebar();
get_footer();
endif;
?>