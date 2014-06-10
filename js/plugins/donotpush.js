//Do Not Push Button 0.0
/**/
define(['jquery'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'doNotPush',
		//===Class
		DoNotPush = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	DoNotPush.prototype = {
		init: function () {
			var that = this;
			$.getScript('http://www.cornify.com/js/cornify.js', function () {
				that.$el.on('click.donotpush', $.proxy(that.handleClick, that));
			});
		},
		handleClick: function (e) {
			window.cornify_add();
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
				$this.data(pluginName, (data = new DoNotPush(this, options)));
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
		$('#do-not-push').doNotPush();
	});
});