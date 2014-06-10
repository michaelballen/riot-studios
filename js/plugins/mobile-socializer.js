//Mobile Socializer 0.0
define(['jquery'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'mobileSocializer',
		$body = $('body'),
		//===Class
		MobileSocializer = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	MobileSocializer.prototype = {
		init: function () {
			var that = this;
			$body
				.on('click.mobilesocial', '[data-sm_mobile_toggle]', that.toggle)
				.on('pageLoading.mobileSocial', function () {
					$body.removeClass('mobile-social-on');
				});
		},
		toggle: function (e) {
			$body.toggleClass('mobile-social-on');
			e.preventDefault();
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new MobileSocializer(this, options)));
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
		$('#mobile-social').mobileSocializer();
	});
});