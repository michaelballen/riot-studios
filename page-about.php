<?php

$module = "about";
$Riot->page_description = 'Learn more about Riot Studios team, films, and mission';

if (!$Riot->isAjax()) :
get_header();
?>
<div class="default-content">
<?php
endif;//ajax if stmt

$team_qry = new WP_Query(array(
	'post_type'=>'team_member',
	'meta_query'=>array(
		array(
			'key' => 'riot_position',
			'value' => '',
			'compare' => '!=',
		)
	),
	'orderby'=>'menu_order'
));
$project_qry = new WP_Query(array(
	'post_type'=>'project',
	'orderby'=>'menu_order'
));
?>
	<div class="content-outer about" data-module="<?php echo $module; ?>" id="scrollable" data-title="<?php $Riot->do_title(); ?>">
		<div class="content-inner" role="main">
			<img src="<?php bloginfo('template_directory') ?>/img/about-header.jpg" class="full-width header-image" />
			<section id="riot-team">
				<h2><span class="title"><span class="ifont ifontusers"></span> Team</span></h2>
				<?php
				if ($team_qry->have_posts()) :
					while($team_qry->have_posts()) : $team_qry->the_post();
				?>
					<article>
						<header>
							<h3><?php the_title(); ?></h3>
							<h4><?php echo get_post_meta(get_the_ID(), 'riot_position', true); ?></h4>
						</header>
						<div class="left-column">
							<?php the_post_thumbnail('large'); ?>
						</div>
						<div class="text-con">
						<?php the_content(); ?>
						</div>
						<?php
						?>
					</article>
				<?php	
					endwhile;
				endif;
				?>
			</section>
				<?php
				if ($project_qry->have_posts()) :
				?>
				<section id="riot-films">
					<h2><span class="title"><span class="ifont ifontfilm"></span> Films</span></h2>
				<?php
					while($project_qry->have_posts()) : $project_qry->the_post();
						$website_link = get_post_meta(get_the_ID(), '_website_link', true);
						$buy_link = get_post_meta(get_the_ID(), '_buy_link', true);
						$stream_link = get_post_meta(get_the_ID(), '_stream_link', true);
				?>
				<article>
					<header>
						<h3><?php the_title(); ?></h3>
					</header>
					<div class="left-column">
						<?php the_post_thumbnail('large'); ?>
						<ul>
						<?php
						if (!empty($website_link)) :
						?>
							<li><a class="btn" href="<?php echo $website_link; ?>" target="_blank">Website</a></li>
						<?php
						endif;
						if (!empty($stream_link)) :
						?>
							<li><a class="btn" href="<?php echo $stream_link; ?>" data-ajax_load>Stream it Online</a></li>
						<?php
						endif;
						if (!empty($buy_link)) :
						?>
							<li><a class="btn" href="<?php echo $buy_link; ?>" data-ajax_load>Buy it Now</a></li>
						<?php
						endif;
						?>
						</ul>
					</div>
					<div class="text-con">
						<?php the_content(); ?>
					</div>
				</article>
				<?php
					endwhile;
				?>
			</section>
				<?php
				endif;
				?>		
			<section id="riot-mission">
				<h2><span class="title"><span class="ifont ifontcrosshair"></span> Mission</span></h2>
				<?php
				wp_reset_postdata();
				the_content();
				?>
			</section>
		<!--!end content inner-->	
		</div>
		<div class="nav-container">
			<nav>
				<ul id="about-nav">
					<li><a href="#riot-team"><span class="ifont ifontusers"></span><span class="hide-mobile"><?php _e('Team'); ?></span></a></li>
					<li><a href="#riot-films"><span class="ifont ifontfilm"></span><span class="hide-mobile"><?php _e('Films'); ?></span></a></li>
					<li><a href="#riot-mission"><span class="ifont ifontcrosshair"></span><span class="hide-mobile"><?php _e('Mission'); ?></span></a></li>
				</ul>
			</nav>
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