//Mobile Post Previewer 0.0
/*jslint nomen: true, devel: true, browser: true*/
define(['jquery'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'mobilePostPreview',
		//===Class
		MobilePostPreviewer = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	MobilePostPreviewer.prototype = {
		init: function () {
			var that = this;
			this.id = this.$el.data('id');
			this.$infoCon = $('#mobile-info-con-' + this.id);
			this.$open_btn = this.$el.find('[data-mobile-open]').on('click.mobilePostPreview', $.proxy(that.showPreview, that));
		},
		showPreview: function () {
			var that = this,
				$current_on = $('.post.previewing');
			if ($current_on.length && $current_on.data('id') === that.$el.data('id')) {
				return false;
			} else if ($current_on.length && $current_on.data('id') !== that.$el.data('id')) {
				$('[data-mobile-close]').trigger('click');
			}
			that.$el.addClass('previewing');
			that.$infoCon.addClass('previewing');
			$('[data-mobile-close]').one('click.mobilePostPreview', $.proxy(that.hidePreview, that));
			$('body').one('pageLoading.mobilePostPreview', $.proxy(that.hidePreview, that));
		},
		hidePreview: function () {
			this.$el.removeClass('previewing');
			this.$infoCon.removeClass('previewing');
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new MobilePostPreviewer(this, options)));
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
		var initPreviewing = function () {
			$('.post').mobilePostPreview();
		};
		$('body').on('pageLoading.' + pluginName, function () {
			$('.post.previewing').removeClass('previewing');
		}).on('pageLoaded.' + pluginName, initPreviewing);
		initPreviewing();
	});
});