<?php
global $Riot, $do_main_header, $wait_for_js;

$wait_for_js = false;

do_html_tag();
?>
<head>
	<meta charset="utf-8">
	<title>Riot Studios | Coming Tuesday</title>
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<meta name="viewport" content="width=320, initial-scale=1, maximum-scale=1">
	<?php $Riot->doAppleIconTags(); ?>
	<?php $Riot->doOpenGraphTags('Riot Studios'); ?>
	<?php wp_head(); ?>
</head>
<body data-rooturl="<?php echo bloginfo('url'); ?>" data-templateurl="<?php echo bloginfo('template_directory'); ?>" class="launch-page js-loaded">
	<div id='fb-root'></div>
	<!--[if lt IE 8]>
	<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
	<![endif]-->
	<div class="wrapper wood launch-page">
		<div class="wood-header"></div>
		<div class="container">
			<h2 class="top-header"><?php _e("We've checked with the Mayans:");?></h2>
			<img class="main-graphic" src="<?php bloginfo('template_directory'); ?>/img/launch-page@2x.png" alt="The Brand New Riot Studios Launches" />
			<p class="time"><?php _e('Tuesday, 8pm CST.'); ?><br><span class="note"><?php _e('(Check back then.)'); ?></span></p>			
			
			<div class="white-container">
				<h2><?php _e('Be the first to the Party'); ?></h2>
				<a class="btn btn-primary btn-large" href="http://riot.nationbuilder.com/apply" target="_blank"><?php _e('Become an Official RIOTER'); ?></a>
			</div>
			
		</div>
		<div class="wood-footer" style="height:10px;"></div>
	</div>
	<script>
	    var _gaq=[['_setAccount','UA-26882346-1'],['_trackPageview']];
	    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
	    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	    s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>
	<?php wp_footer(); ?>
</body>
</html>