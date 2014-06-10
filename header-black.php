<?php
global $Riot, $do_main_header, $wait_for_js;

$wait_for_js = false;
do_html_tag();
?>
<head>
	<meta charset="utf-8">
	<title><?php $Riot->do_title(); ?></title>
	<meta name="description" content="<?php _e($Riot->page_description); ?>">
	<meta name="viewport" content="width=320, initial-scale=1, maximum-scale=1">
	<?php wp_head(); ?>
</head>
<body data-rooturl="<?php echo bloginfo('url'); ?>" data-templateurl="<?php echo bloginfo('template_directory'); ?>" <?php if ($do_main_header === false) {
	echo 'class="no-main-header"';
} ?>>
	<?php do_chromeframe(); ?>
	<div class="wrapper gray-texture">