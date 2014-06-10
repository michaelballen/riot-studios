//Plugin Name 0.0
/*global Modernizr*/
require(['jquery', 'vendor/bootstrap/bootstrap-transition'], function ($) {
	"use strict";
	var $cover,
		removeCover = function () {
			if (Modernizr.csstransitions && Modernizr.csstransforms) {
				$cover.one($.support.transition.end, function () {
					$(this).remove();
				});
				$('html').removeClass('js-unloaded');
			} else {
				$cover.remove();
			}
		};
	$(function () {
		$cover = $('#loading-cover');
		if (Modernizr.riotHead) {
			return removeCover();
		}
		$(window).one('hasRiotHead', removeCover);
	});
});