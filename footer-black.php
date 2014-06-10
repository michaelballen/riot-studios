<?php global $module, $Riot; ?>
		<!--! end wrapper div-->
		</div>
		<div id="page-loading"></div>
		<script>
		    /*var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
		    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
		    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		    s.parentNode.insertBefore(g,s)}(document,'script'));*/
		</script>
		<?php wp_footer(); ?>
		<script src="<?php bloginfo('template_directory'); ?>/js/require-jquery.js" data-main="<?php bloginfo('template_directory'); ?>/js/<?php echo $module; ?>"></script>
	</body>
</html>