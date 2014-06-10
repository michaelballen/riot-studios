//Auto Scroller 0.0
//for auto scrolling to different anchor tags
!(function ($) {
	"use strict";
	//definitions
	var pluginName = 'autoScroller',
		//===Class
		AutoScroller = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(),  options);
			this.init();
		};
	AutoScroller.prototype = {
		init: function () {
			var that = this;
			this.$parent = this.$el.parents('.auto-scroll-container');
			this.$a = this.$el.find('a').on('click.autoScroller', function (e) {
				var $this = $(this),
					$anchor = $this.data('anchor'),
					top = 100;
				e.preventDefault();
				if ($anchor === undefined) {
					$anchor = $($this.attr('href'));
					$this.data('anchor', $anchor);
				}
				if (!$anchor.length) {
					return false;
				}
				if (!Modernizr.touch){
					$this.addClass('active');
					top = $anchor.offset().top - 32;
					that.$parent.animate({
						scrollTop: (top + that.$parent.scrollTop())
					}, 300);
					window.location.hash = $anchor[0].id;
				}
			});
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new AutoScroller(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$('[data-auto_scroll]').autoScroller();
	});
}(this.jQuery));