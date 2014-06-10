//Scroller 0.0
//make the innercontent scroll the way its supposed to
/*global Modernizr, iScroll*/
define(['jquery', 'vendor/iscroll-lite'], function ($) {
	"use strict";
	var myScroll,
		refreshScroll = function () {
			if (typeof myScroll === 'object' && typeof myScroll.refresh === 'function') {
				myScroll.refresh();
			}
		},
		setupScroll = function () {
			var $sClass = $('.scrollable'),
				cScroll,
				$mainScrollable = $('#scrollable');
			if ($mainScrollable.length) {
				if ($mainScrollable.hasClass('has-form')) {
					myScroll = new iScroll('scrollable', {
						useTransform: false,
						onBeforeScrollStart: function (e) {
							var target = e.target;
							while (target.nodeType !== 1) {
								target = target.parentNode;
							}
							if (target.tagName !== 'SELECT' && target.tagName !== 'INPUT' && target.tagName !== 'TEXTAREA') {
								e.preventDefault();
							}
						}
					});
				} else {
					myScroll = new iScroll('scrollable');
				}
			}
			if ($sClass.length) {
				$sClass.each(function () {
					var $this = $(this);
					cScroll = new iScroll($this[0]);
					$this.find('img').off('load.touchScroll').on('load.touchScroll', refreshScroll);
				});
			}
			$('body').trigger('scrollerReady');
			setTimeout(function () {
				// Already scrolled?
				if (window.pageYOffset !== 0) {
					return;
				}
				window.scrollTo(0, window.pageYOffset + 1);
			}, 1);
		};
	if (Modernizr.touch) {
		//===Data-API
		document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
		$(setupScroll);
		$('body').on('pageLoaded.scroller', setupScroll)
			.on('scrollCheck', setupScroll)
			.on('refreshScroll', refreshScroll);
	}
});