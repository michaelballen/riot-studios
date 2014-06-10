//Image Rotator 0.0
/*global Modernizr*/
define(['jquery', 'vendor/bootstrap/bootstrap-transition'], function ($) {
	"use strict";
	//definitions
	var $body,
		pluginName = 'imageRotator',
		//===Class
		ImageRotator = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	ImageRotator.prototype = {
		init: function () {
			var that = this;
			this.currentIndex = 0;
			this.$ul = this.$el.find('ul');
			this.$li = this.$ul.find('li');
			this.$current = $(this.$li[0]);
			if (Modernizr.touch) {
				this.$el.on('touchstart.imageRotator', function () {
					that.$el.one('touchend.imageRotator', $.proxy(that.next, that)).one('touchmove.imageRotator', function () {
						that.$el.off('touchend.imageRotator');
					});
				});
			} else {
				this.$el.on('click.imageRotator', $.proxy(this.next, this));
			}
			if (this.options.autoScroll) {
				this.setTimer();
			}
		},
		setTimer: function () {
			this.timer = setTimeout($.proxy(this.next, this), this.options.autoScroll);
		},
		next: function () {
			var that = this,
				nextI = 0,
				$next,
				$current;
			if (this.animating) {
				return false;
			}
			$current = this.$current;
			if (this.timer) {
				clearTimeout(this.timer);
				this.timer = null;
			}
			if (this.currentIndex < this.$li.length - 1) {
				nextI = this.currentIndex + 1;
			}
			$next = $(this.$li[nextI]);
			if ($.support.transition) {
				this.animating = true;
				$next.addClass('right on');
				$next[0].offsetWidth;
				$next.removeClass('right');
				$current.addClass('left');
				this.$current.one($.support.transition.end, function () {
					that.$current = $next.addClass('current');
					$current.removeClass('on left current');
					that.animating = false;
					that.setTimer();
				});
			}
			this.currentIndex = nextI;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new ImageRotator(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	$.fn[pluginName].defaults = {
		autoScroll: 4000
	};
	//===Data-API
	$(function () {
		var setupRotator = function () {
			$('.image-rotator').imageRotator();
		};
		$body = $('body').on('pageLoaded.imageRotator', function (e, page) {
			if (page === 'about') {
				setupRotator();
			}
		});
		setupRotator();
	});
}(this.jQuery));