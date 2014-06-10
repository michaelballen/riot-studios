//Twitter 0.0
/*jslint nomen: true, devel: true, browser: true*/
(function ($) {
	"use strict";
	//definitions
	var pluginName = 'twitter',
		//===Class
		Twitter = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.$ul = this.$el.find('ul');
			this.getTimeline({
				screen_name: this.options.screen_name,
				count: 20
			});
		};
	Twitter.prototype = {
		ajaxCall: function (opt) {
			$.ajax({
				url: opt.url,
				dataType: 'jsonp',
				async: false,
				data: opt.data,
			}).done(function (r) {
				if (opt.hasOwnProperty('callback') && typeof opt.callback === 'function') {
					opt.callback(r);
				}
			});
		},
		getTimeline: function (opt) {
			var that = this;
			this.ajaxCall({
				url: 'https://api.twitter.com/1/statuses/user_timeline.json',
				data: {
					include_entities: true,
					include_rts: true,
					screen_name: opt.screen_name,
					count: opt.count
				},
				callback: function (r) {
					var i;
					for(i = 0; i < r.length; i += 1){
						that.$ul.append('<li>' + r[i].text + '</li>')
					}
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
				$this.data(pluginName, (data = new Twitter(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	$.fn[pluginName].defaults = {
	};
	//===Data-API
	$(function () {
		$('.twitter').twitter();
	});
}(this.jQuery));