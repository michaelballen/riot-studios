//Plugin Name 0.0
define(['jquery', 'vendor/imagesloaded', 'vendor/bootstrap/bootstrap-transition', 'plugins/ajax-loader'], function ($) {
	"use strict";
	//===Data-API
	$(function () {
		var $body = $('body'),
			$nav,
			$nav_links,
			$content_inner,
			afterLoad = false,
			setupAnchors = function () {
				if ($body.hasClass('page-loaded')) {
					$body.one('pageTransitioned', function () {
						afterLoad = false;
						setupAnchors();
					});
					return false;
				}
				$nav_links.each(function () {
					var $this = $(this),
						$linkedTo = $($this.attr('href'));
					if ($linkedTo.length) {
						$this.data('topScroll', $linkedTo.offset().top - 60);
					}
				});
				//disable click events on anchors
				if (Modernizr.touch) {
					$nav_links.on('click.aboutNav', function (e) {
						e.preventDefault();
					});
					$nav_links.on('touchend.aboutNav', function () {
						$content_inner[0].style[Modernizr.prefixed('transitionDuration')] = '0.4s';
						$content_inner[0].style[Modernizr.prefixed('transform')] = 'translate(0px, -' + $(this).data('topScroll') + 'px)';
						$content_inner.one($.support.transition.end, function () {
							$content_inner[0].style[Modernizr.prefixed('transitionDuration')] = '0ms';
						});
					});
				}
			},
			setupAboutPage = function () {
				$nav = $('#about-nav'),
				$nav_links = $nav.find('a'),
				$content_inner = $('.content-inner');
				if (Modernizr.touch) {
					$content_inner.scrollTop(0,0);
					setupAnchors();
				}
			};
		setupAboutPage();
	});
});