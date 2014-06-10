<?php
/*
Template Name: Show Posts
*/

$squares_per_page = 19;
$module = 'home';
global $wp_query, $Riot, $page_title;

if (is_front_page() || get_the_title() === 'Hello world!') {
	$page_title = 'RIOT STUDIOS';
} else {
	$page_title = get_the_title();
	$Riot->page_description = 'See the latest videos and images from Riot Studios';
}
/*
if (get_option('show_landing_page') === 'yes' && !is_user_logged_in() && !is_login_page()) {
	require_once('templates/launch-page.php');
	exit();
}
*/

if (!$Riot->isAjax()) :
	get_header();
?>
<div class="default-content">
<?php endif; ?>
	<div class="content-outer home<?php
	if (strtolower($wp_query->post->post_title) === 'media') {
		echo ' media-page';
	}
	if (is_front_page()) {
		echo ' front-page';
	}
	if ($wp_query->post->post_type === 'video' || $wp_query->post->post_type === 'article') {
		echo ' trans';
	}
	?>" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner" id="content-inner">
			<?php
			if (is_front_page()):
				$home_slide_qry = new WP_Query(array(
					'post_type'=>'home_slide',
					'posts_per_page'=>0
				));
				if ($home_slide_qry->have_posts()) :
			?>
			<header id="home-slider">
				<div class="slider-inner">
					<ul>
						<?php while ($home_slide_qry->have_posts()) : $home_slide_qry->the_post(); ?>
						<li>
							<?php
							$link = get_post_meta(get_the_ID(), '_link', true);
							$riot_post_id = get_post_meta(get_the_ID(), '_riot_id', true);
							$riot_post_type = get_post_meta(get_the_ID(), '_link_post_type', true);
							if (!empty($link)) {
								echo '<a title="' . get_the_title() . '" href="' . $link . '"';
								if (!empty($riot_post_id)) {
									$home_slide_data_attr = getDataAttr($riot_post_type, $riot_post_id);
									echo ' data-riot_post_id="' . $riot_post_id . '"';
									if ($home_slide_data_attr !== false) {
										echo ' ' . $home_slide_data_attr;
									} else {
										echo ' data-ajax_load';
									}
								} else {
									echo ' target="_blank"';
								}
								echo '>';
							}
							the_post_thumbnail('home-slide', array(
								'alt'=>get_the_title()
							));
							if (!empty($link)) {
								echo '</a>';
							}
							?>
						</li>
						<?php endwhile; ?>
					</ul>
					<a href="#" class="arrow prev hidden"><span class="ifontarrow-left"></span></a>
					<a href="#" class="arrow next"><span class="ifontarrow-right"></span></a>
				</div>
			</header>
				<?php endif;//if have posts for home slide qry ?>
			<?php endif; ?>
		<?php
		$pagename = get_the_title();
		$query = new WP_Query(array(
			'post_type' => getPostTypes($pagename),
			'posts_per_page'=>$squares_per_page,
			'orderby'=>'date'
		));
		$i = 0;
		while ($query->have_posts()) :
			$query->the_post();//start the loop
			do_home_square($i);
			$i += 1;
		endwhile;
		
		if ($query->max_num_pages != 1) :
		?>
		<div class="read-more-container">
			<a href="#" data-load_home_squares data-number="<?php echo $squares_per_page; ?>" data-paged="2" data-type="<?php
			if (!is_front_page()) {
				echo 'media';
			} else {
				echo 'main';
			} ?>" class="btn btn-large"><span class="no-progress">Load More Content</span><span class="progress"><span class="loading"></span> Loading More Content&hellip;</span></a>
		</div>
		<?php
		endif;
		?>
		
		<!--!end content inner-->	
		</div>
		
		<div class="fixed-con hide-desktop">
			<?php
			$query->rewind_posts();
			while ($query->have_posts()) : $query->the_post();//start the loop
				$pt = get_post_type();
				$data_attr = getDataAttr($pt, $post);
				if ($pt === 'video') {
					$icon_name = "m";
				} else if ($pt === 'image') {
					$icon_name = "p";
				} else {
					$icon_name = "e";
				}
			?>
			<div class="info-con" id="mobile-info-con-<?php the_ID(); ?>" data-id="<?php the_ID(); ?>" data-meta='<?php echo $Riot->makePostMeta($post); ?>'>
				<div data-mobile-close>
					<?php $Riot->do_three_lines(); ?>
				</div>
				<div class="inner-info">
					<h2><? the_title(); ?></h2>
					<p class="hide-in-mobile align-center i-con">
						<a title="<?php the_title(); ?>" <?php echo $data_attr; ?> href="<?php the_permalink(); ?>"><span class="ifont"><?php echo $icon_name; ?></span></a>
					</p>
					<h3><? echo getAuthorText(get_post_type(), get_the_author()); ?></h3>
					<p><a title="<?php the_title(); ?>" <?php						
						echo $data_attr;
						?> href="<? the_permalink(); ?>"><?= wp_trim_words(get_the_content(), 22); ?></a></p>
					<p class="align-center read-more">
						<a title="<?php the_title(); ?>" class="btn btn-primary" <?php						
						echo $data_attr;
						?> href="<? the_permalink(); ?>"><?= viewLink(get_post_type()); ?></a>
					</p>
				</div>
			</div>
			<?php
			endwhile;
			?>
			
		</div><!--! end .content-inner -->
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