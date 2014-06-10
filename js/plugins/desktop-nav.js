//Desktop Nav 0.0
/*global Modernizr*/
!(function ($) {
	"use strict";
	//definitions
	var $body,
		pluginName = 'desktopNav',
		//===Class
		DesktopNav = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	DesktopNav.prototype = {
		init: function () {
			var that = this,
				handleButtonClick = function () {
					var $this = $(this),
						kClass = $this.data('knob_class'),
						oldClass = '',
						$tm,
						$tma,
						$siblings,
						removeActiveTicker = function () {
							$tma.addClass('off').removeClass('active out');
							$tma[0].offsetWidth;
							$tma.removeClass('off');
						};
					if ($this.hasClass('active')) {
						return false;
					}
					if (that.$active_btn && that.$active_btn.length) {
						$('[data-knob_class].active').removeClass('active');
						that.$active_btn.removeClass('active');
						oldClass = that.$active_btn.data('knob_class');
					}
					$siblings = $('[data-knob_class="' + kClass + '"]').addClass('active');
					$this.addClass('active');
					that.$knob.removeClass(oldClass).addClass(kClass);
					that.$active_btn = $this;
					$tm = $this.data('ticker-mate');
					if ($tm.length) {
						$tma = that.$ticker.find('.active');
						if ($tma.hasClass('out')) {
							removeActiveTicker();
						} else {
							if ($.support.transition) {
								that.$ticker.find('.active').addClass('out').one($.support.transition.end, removeActiveTicker);
							}
						}
						$tm.addClass('active').removeClass('hover');
					}
				},
				handleButtonHover = function () {
					var $this = $(this);
					$this.data('ticker-mate').addClass('hover');
					that.$ticker.find('.active').addClass('out');
				},
				handleButtonLeave = function () {
					var $this = $(this);
					$this.data('ticker-mate').removeClass('hover');
					that.$ticker.find('.active').removeClass('out');
				};
			this.currentURL = null;
			if (window.location.href.indexOf('checkout') > 0) {
				$('[data-knob_class="store"]').addClass('active');
			}
			that.$active_btn = $('[data-knob_class].active');
			that.$homeDisplay = $('#home-icon');
			that.$knob = that.$el.find('.chrome-knob');
			if (Modernizr.touch) {
				that.$buttons = $('[data-knob_class]')
					.on('click.desktopNav', function (e) {
						e.preventDefault();
						return false;
					}).on('touchend.desktopNav', handleButtonClick);
			} else {
				that.$buttons = $('[data-knob_class]')
					.on('click.desktopNav', handleButtonClick)
					.on('mouseenter.desktopNav', handleButtonHover)
					.on('mouseleave.desktopNav', handleButtonLeave);
			}
			that._setupTicker();
		},
		_setNewTicker: function (title) {
			var $temp = this.$ticker.find('.temp'),
				$def = this.$ticker.find('.default-active');
			$temp.addClass('default-active').removeClass('temp').text(title);
			$def.removeClass('default-active').addClass('temp');
		},
		_setupTicker: function () {
			var $newTick,
				$tickOuter,
				tickText;
			this.$ticker = this.$el.find('.nav-ticker');
			$tickOuter = this.$ticker.find('.outer-window');
			this.$buttons.each(function () {
				var $this = $(this);
				$newTick = $('<div />');
				tickText = $this.data('tick_text') || $this.find('.title').text();
				$tickOuter.append($newTick.text(tickText));
				$this.data('ticker-mate', $newTick);
			});
			if (this.$active_btn.length) {
				this.$active_btn.data('ticker-mate').addClass('active');
			}
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new DesktopNav(this, options)));
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
		$body = $('body');
		$('#main-nav').desktopNav();
	});
}(this.jQuery));