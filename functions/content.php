<?php
function getDataAttr ($pt, $post) {
	if (is_object($post)) {
		$post_id = $post->ID;
	} else {
		$post_id = $post;
	}
	if ($pt === 'video') {
		return 'data-play_video="' . get_post_meta($post_id, '_vidid', true) . '"'; 
	}
	if ($pt === 'article') {
		return 'data-article_theater';
	}
	if ($pt === 'image') {
		return 'data-image_theater=' . $post_id;
	}
	return false;
}
function getAuthorText ($type, $author = FALSE) {
	if ($author == false) {
		$author = get_the_author();
	}
	
	if ($type === 'image') {
		return __('Photos from ' . $author);
	}
	return __($type . ' by ' . $author);
}
function viewLink ($type) {
	if ($type === 'video') {
		return 'Watch Now';
	}
	if ($type === 'image') {
		return 'View Image';
	}
	return 'Read More';
}
function getPostTypes ($page_name = false) {
	$types = array();
	if (!empty($page_name) && strtolower($page_name) === 'media') {
		$types = array(
			'video',
			'image'
		);
	} else {
		$types = array(
			'article',
			'video',
			'image',
			'home_square'
		);
	}
	return $types;
}
function do_main_menu_item ($title, $article_theater=false) {
	global $Riot, $knob_class;
	$class_arr = array(
		'about'=>'ifontinfo',
		'blog'=>'ifontblog',
		'media'=>'ifontmedia',
		'store'=>'ifontcart'
	);
?>
	<li class="<?php echo $title; if ($Riot->testURLMatch(site_url($title), $Riot->currentURL(), true) && !$article_theater) {
		echo ' active';
		$knob_class = $title;
	} ?>" data-knob_class="<?php echo $title; ?>" >
		<a href="<?php echo site_url($title); ?>" data-ajax_load><span class="ifont <?php echo $class_arr[$title]; ?>"></span><span class="active"></span><span class="inactive"></span><span class="title"><?php _e(ucfirst($title)); ?></span></a>
	</li>
<?php
}
function getHomeSquareClasses ($i, $extra = false) {
	$class_arr = array();
	$class_str = '';
	
	//become 1-indexed
	$i += 1;
	
	//figure out where we are in each setting
	$three_col = $i % 12;
	$four_col = $i % 18;
	$five_col = $i % 24;
	
	if ($extra !== false) {
		$class_arr[] = $extra;
	}
	
	//set special classes
	if ($i === 1) {
		$class_arr[] = 'first-post';
	} else if ($i === 2) {
		$class_arr[] = 'second-post';
	} else if ($i === 3) {
		$class_arr[] = 'third-post';
	} else if ($i === 3) {
		$class_arr[] = 'third-post';
	} else if ($i === 4) {
		$class_arr[] = 'fourth-post';
	}
	
	//set 3-column classes
	if ($three_col === 1 || $three_col === 8) {
		$class_arr[] = 'c3-large';
	}
	if ($three_col === 8) {
		$class_arr[] = 'c3-fltrt';
	}
	if ($three_col === 4 || $three_col === 10) {
		$class_arr[] = 'c3-clrlt';
	}
	
	//set 4-column classes
	if ($i == 1) {
		$class_arr[] = 'c4-tl-radius';
	}
	if ($i == 3) {
		$class_arr[] = 'c4-tr-radius';
	}
	if ($four_col === 1 || $four_col === 12) {
		$class_arr[] = 'c4-large';
	}
	if ($four_col === 12) {
		$class_arr[] = 'c4-fltrt';
	}
	if ($four_col === 6 || $four_col === 15) {
		$class_arr[] = 'c4-clrlt';
	}
	
	//set 4-column classes
	if ($i == 1) {
		$class_arr[] = 'c5-tl-radius';
	}
	if ($i == 4) {
		$class_arr[] = 'c5-tr-radius';
	}
	if ($five_col === 1 || $five_col === 16) {
		$class_arr[] = 'c5-large';
	}
	if ($five_col === 16) {
		$class_arr[] = 'c5-fltrt';
	}
	if ($five_col === 20 || $five_col === 8) {
		$class_arr[] = 'c5-clrlt';
	}
	
	//implode the array
	if (!empty($class_arr)) {
		$class_str = implode(' ', $class_arr);
	}
	
	return $class_str;
}
function do_home_square ($i, $echo=true) {
	global $post, $Riot;
	$square_string = '';
	$pt = get_post_type();
	$data_attr = getDataAttr($pt, $post);
	$link = false;
	$riot_link = false;
	$extra_class = false;
	if ($i === $query->post_count - 1) {
		$extra_class = 'last-post';
	}
	if ($i === $query->post_count - 2) {
		$extra_class = 'last-post2';
	}
	if ($i === $query->post_count - 3) {
		$extra_class = 'last-post3';
	}
	$class_str = getHomeSquareClasses($i, $extra_class);
	if ($pt === 'video') {
		$icon_name = "m";
	} else if ($pt === 'image') {
		$icon_name = "p";
	} else {
		$icon_name = "e";
	}
	$square_string .= '<article id="article-' . get_the_ID() . '" data-index="' . $i . '" class="post unloaded ' . get_post_type($post) . ' ' . $class_str . '" data-id="' . get_the_ID() . '">';
		if ($pt === 'home_square') :
			$link = get_post_meta(get_the_ID(), '_link', true);
			if (!empty($link)) :
				$riot_link = get_post_meta(get_the_ID(), '_riot_id', true);
				$square_string .= '<a href="' . $link . '">';
			endif;
		endif;
		$square_string .= '<div class="img-con" data-mobile-open>';
		if (has_post_thumbnail()) {
			if (strpos($class_str, '-large')) {
				$imgdata = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large-square' );
				if ($imgdata[1] === $imgdata[2]) {
					$square_string .= get_the_post_thumbnail($post->ID, 'large-square');
				} else {
					$square_string .= get_the_post_thumbnail($post->ID, 'thumbnail');
				}
			} else {
				$square_string .= get_the_post_thumbnail($post->ID, 'medium-square');
			}
		} else {
			$square_string .= '<img src="' . get_bloginfo('template_directory'). '/img/blue-ph.jpg" />';
		}
		if ($pt !== 'home_square') :
			$square_string .= '<div class="preview-cover">';
			$square_string .= '<a title="' . get_the_title() . '" ' . $data_attr. ' href="' . get_permalink() . '" class="preview-link ifont"><span>' . $icon_name . '</span></a>';
			$square_string .= '<h2 class="preview-title"><a title="' . get_the_title() . '"' . $data_attr . 'href="' . get_permalink() . '"><span>' . get_the_title() . '</span></a></h2>';
			$square_string .= '</div>';
		endif;
		$square_string .= '</div>';
		if ($pt === 'home_square' && !empty($link)) {
			$square_string .= '</a>';
		}
	$square_string .= '</article>';
	if ($echo === false) {
		return $square_string;
	} else {
		echo $square_string;
		return true;
	}
}
function do_main_header () {
	global $knob_class, $wp_query;
	$knob_class = false;
	$post_type = $wp_query->post->post_type;
	$theater_types = array(
		'video',
		'image',
		'article'
	);
	
	if ($wp_query->is_single) {
		$preload_theater = in_array($post_type, $theater_types);
	} else {
		$preload_theater = false;
	}
	
	if ($preload_theater) {
		$knob_class = 'home';
	}
?>
<header id="main-nav" role="banner">
	<!--begin nav section-->
		<nav class="nav-menu" role="navigation">
			<a class="logo" href="<? bloginfo('url') ?>" data-ajax_load data-knob_class="home" data-tick_text="Home">Riot</a>
			<div class="measure-line show-mobile hidden"></div>
			<a href="<? bloginfo('url'); ?>" id="home-icon" data-tick_text="Home" data-knob_class="home" data-ajax_load class="home-con hide-mobile<?php if (is_front_page() || $preload_theater) {
				echo ' active';
			} ?>">
				<span class="ifonthome"></span>
			</a>
			<ul>
				
			<?php
			$menu_items = array(
				'about',
				'blog',
				'media',
				'store'
			);
			foreach($menu_items as $v) {
				do_main_menu_item($v, $preload_theater);
			}
			?>
			</ul>
		</nav>
		<div class="chrome-knob hide-mobile<?php if ($knob_class) {
			echo ' ' . $knob_class;
		} ?>">
			<div class="chrome-knob-pointer"></div>
			<div class="chrome-knob chrome-knob-inner"></div>
		</div>
		<div class="nav-ticker">
			<div class="outer-window">
			</div>
		</div>
		<!-- You know you want to...-->
		<div id="do-not-push" class="hide-mobile">Do Not Push</div>
		
<!--! end #main-nav-->
</header>
<?php
}
function do_html_tag () {
	global $wp_query, $wait_for_js;
	$class = ' no-js';
	if ($wp_query->is_single) {
		if ($wp_query->post->post_type === 'video') {
			$class .= ' video-theater';
		} else if ($wp_query->post->post_type === 'image') {
			$class .= ' image-theater';
		} else if ($wp_query->post->post_type === 'article') {
			$class .= ' article-theater';
		}
	}
	if ($wait_for_js !== false) {
		$class .= ' js-unloaded';
	}
?>
	<!DOCTYPE html>
	<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7<?php echo $class; ?>"> <![endif]-->
	<!--[if IE 7]>         <html class="lt-ie9 lt-ie8<?php echo $class; ?>"> <![endif]-->
	<!--[if IE 8]>         <html class="lt-ie9<?php echo $class; ?>"> <![endif]-->
	<!--[if gt IE 8]><!--> <html class="<?php echo $class; ?>"><!--<![endif]-->
<?php
}
function do_chromeframe () {
?>
<!--[if lt IE 8]>
<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
<![endif]-->
<?php
}