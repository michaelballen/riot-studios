require(['jquery', 'plugins/loading-cover', 'plugins/donotpush', 'plugins/ajax-loader', 'plugins/mobile-socializer', 'plugins/contact-form', 'plugins/socializer', 'plugins/popout-footer', 'plugins/mobile-footer', 'plugins/desktop-nav', 'plugins/scroller', 'plugins/auto-scroller'], function ($) {
	Modernizr.riotHead = true;
	$(function () {
		$(window).trigger('hasRiotHead');
	});
});