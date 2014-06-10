<?php
$module = 'believe-me';
wp_dequeue_style('screen-css');
wp_dequeue_style('print-css');
wp_enqueue_style('believe-me');
do_html_tag();
?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Believe Me // Coming Soon</title>
        <meta name="description" content="Believe Me - the Newest Film from Riot Studios, Coming Soon">
        <meta name="viewport" content="width=320, initial-scale=1, maximum-scale=1">
		<meta property="og:image" content="http://riotstudios.com/wp-content/themes/riotstudios/img/bm-profile-pic.jpg">
		<meta property="og:url" content="http://believemefilm.com">
		<meta property="og:title" content="Believe Me // Coming 2014">
		<meta property="og:description" content="Believe Me - the Newest Film from Riot Studios, Coming Soon"/>
		<?php wp_head(); ?>
    </head>
    <body data-rooturl="<?php echo site_url(); ?>">
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=201900699822506";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<div class="content-outer" data-module="<?php echo $module; ?>" data-title="<?php $Riot->do_title(); ?>"></div>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
		<div class="main global-width" role="main">
			<header class="believe-me-header">
				<h1><?php _e('Believe Me'); ?></h1>
				<h2><?php _e("It's only a sin if you get caught."); ?></h2>
			</header>
			<div class="bm-form-container">
				<div class="input-append updates-input-group">
					<form id="signup-form">
						<div id="signup-error-label"></div>
						<input name="user_email" class="span2" id="signup-input" type="email" placeholder="Email">
						<button class="btn btn-success" type="button"><span class="before">Sign Up</span><span class="during">Sending&hellip;</span><span class="after"><span class="ifontcheckmark"></span></span></button>
					</form>
				</div>
			</div>
			<div class="legal-container">
				<h2><?php _e('Coming Soon'); ?></h2>
				<div class="menus group">
					<ul class="main-menu">
						<li><a target="_blank" href="<?php echo site_url(); ?>" title="Riot Studios">Riot Studios</a></li>
						<li><a target="_blank" href="http://lascauxfilms.com/" title="Lascaux Films">Lascaux Films</a></li>
						<li><a target="_blank" href="http://en.wikipedia.org/wiki/Believe_Me_(film)" title="Believe Me on Wikipedia">Wiki</a></li>
					</ul>
					<ul class="social-menu">
						<li><a target="_blank" href="http://www.imdb.com/title/tt3107070/combined" class="imdb"><span><img src="<?php bloginfo('template_directory'); ?>/img/imdb-logo.png"></span></a></li>
						<li><a href="http://facebook.com/believemefilm" title="Believe Me on Facebook"><i class="ifontfacebook"></i></a></li>
						<li><a href="http://twitter.com/believemefilm" title="Believe Me on Twitter"><i class="ifonttwitter"></i></a></li>
						<li><a href="http://instagram.com/believemefilm" title="Believe Me on Instagram"><i class="ifontinstagram"></i></a></li>
					</ul>
				</div>
				<div class="credits"><p>Riot Studios presents “Believe Me” in association with Lascaux Films<br>Alex Russell  Zachary Knighton  Johanna Braddy  Miles Fisher  Sinqua Walls  Max Adler<br>and Christopher McDonald with Nick Offerman original music by Hanan Townshend production designer George T. Morrow<br>cinematography by John W. Rutland casting by JC Cantu c.s.a. co-produced by richard toussaint, Gary Cogill and Steve Markham<br>produced by Alex Carroll written by Michael B. Allen and Will Bakke directed by Will Bakke<br>Riot Studios #believemefilm Lascaux Films</p></div>				
				</ul>
			</div>
		</div>
		<?php wp_footer(); ?>
		<script src="<?php bloginfo('template_directory'); ?>/js/require-jquery.js" data-main="<?php bloginfo('template_directory'); ?>/js/<?php echo $module; ?>"></script>
		
		<div id="bm-modal">
			<div class="modal-outer">
				<a href="#" class="close-btn">&times;</a>
				<div class="modal-inner">
					<h2 class="open-sans">You're Officially In</h2>
					<h3>Now, brag about it to your friends. <span class="arrow">&#10549;</span></h3>
					<div class="social-section">
						
						<a href="https://twitter.com/share" class="twitter-share-button" data-text="Has everyone seen Believe Me Film (new project from @riotstudios)" data-via="riotstudios" data-size="large" data-related="believemefilm" data-hashtags="dontgetcaught">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
						
						<div class="fb-like" data-href="http://believemefilm.com" data-send="true" data-width="450" data-show-faces="true"></div>
					</div>
					<div class="last-section">
						<p>To get the full scoop on the &ldquo;Believe Me&rdquo;,<br>make sure to check <a href="<?php echo site_url(); ?>"><?php _e('Riot Studios'); ?></a> daily.</p>
						<a href="<?php echo site_url(); ?>" class="btn btn-primary btn-large">Continue to Riot Studios <span class="ifontarrow-right"></span></a>
					</div>
				</div>
			</div>
			<div class="bg"></div>
		</div>
		<script>
		    var _gaq=[['_setAccount','UA-26882346-1'],['_trackPageview']];
		    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
		    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		    s.parentNode.insertBefore(g,s)}(document,'script'));
		</script>
	</body>
</html>