//Popout Footer 0.0
!(function ($) {
	"use strict";
	//definitions
	var pluginName = 'popoutFooter',
		//===Class
		PopoutFooter = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(),  options);
			this.init();
		};
	PopoutFooter.prototype = {
		init: function () {
			if (Modernizr.touch) {
				$('body').on('click.popoutFooter', '[data-toggle_popout_footer]', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.popoutFooter', '[data-toggle_popout_footer]', $.proxy(this.toggle, this));
			} else {
				$('body').on('click.popoutFooter', '[data-toggle_popout_footer]', $.proxy(this.toggle, this));
			}
			this.$footerBottom = this.$el.find('.footer-bottom');
			if (window.location.hash === '#desktop-footer') {
				this.show();
				window.location.hash = '';
			}
		},
		toggle: function (e) {
			if (typeof e === 'object') {
				e.preventDefault();
			}
			if (this.shown) {
				this.hide();
			} else {
				this.show();
			}
		},
		show: function () {
			var tH;
			this.$el.addClass('show-bottom');
			if (Modernizr.csstransforms) {
				this.$el[0].offsetWidth;
				tH = this.$footerBottom.height();
				this.$el[0].style[Modernizr.prefixed('transform')] = 'translate(0, -' + tH + 'px)';
			}
			this.shown = true;
		},
		hide: function () {
			var that = this;
			if (Modernizr.csstransforms) {
				this.$el.one($.support.transition.end, function () {
					that.$el.removeClass('show-bottom');
				})[0].style[Modernizr.prefixed('transform')] = 'translate(0,0px)';
			} else {
				that.$el.removeClass('show-bottom');
			}
			that.shown = false;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new PopoutFooter(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$('#desktop-footer').popoutFooter();
	});
}(this.jQuery));