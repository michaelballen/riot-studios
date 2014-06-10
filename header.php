<?php
global $Riot, $do_main_header;
do_html_tag();
?>
<head>
	<meta charset="utf-8">
	<title><?php $Riot->do_title(); ?></title>
	<meta name="description" content="<?php _e($Riot->page_description); ?>">
	<meta name="keywords" content="<?php _e($Riot->page_keywords); ?>">
	<meta name="viewport" content="width=320, initial-scale=1, maximum-scale=1">
	<link href="https://plus.google.com/109293430483697598583" rel="publisher" />
	<?php $Riot->doAppleIconTags(); ?>
	<?php $Riot->doOpenGraphTags(); ?>
	<?php wp_head(); ?>
	<script type="text/javascript">
	  (function() {
	    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
	    po.src = "https://apis.google.com/js/plusone.js?publisherid=109293430483697598583";
	    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
</head>
<body data-rooturl="<?php echo bloginfo('url'); ?>" data-templateurl="<?php echo bloginfo('template_directory'); ?>" <?php if ($do_main_header === false) {
	echo 'class="no-main-header"';
} ?>>
	<div id='fb-root'></div>
	<!--[if lt IE 8]>
	<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
	<![endif]-->
	<div id="loading-cover">
		<div class="logo"></div>
		<div class="dots">
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</div>
		<span class="load-title">Loading Site&hellip;</span>
	</div>
	<div class="wrapper">
		<div class="wood-header"></div>
		<div class="wood-footer"></div>
		<?php
		if ($do_main_header !== false) {
			do_main_header();
		}
		?>