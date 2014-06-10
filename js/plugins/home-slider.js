//Riot Home Slider 0.0
/*global Modernizr*/
define(['jquery', 'plugins/riot-plugins'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'riotHomeSlider',
		//===Class
		RiotHomeSlider = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, options);
			this.init();
		};
	RiotHomeSlider.prototype = {
		init: function () {
			var that = this,
				onImagesReady = function () {
					that.ratio = that.$li.width() / that.$li.height();
					that.handleResize();
				},
				preventClick = function (e) {
					e.preventDefault();
				};
			this.currentIndex = 0;
			this.$arrowRight = this.$el.find('a.arrow.next');
			this.$arrowLeft = this.$el.find('a.arrow.prev');
			if (!Modernizr.touch) {
				this.$arrowRight.on('click.homeSlider', $.proxy(this.nextSlide, this));
				this.$arrowLeft.on('click.homeSlider', $.proxy(this.prevSlide, this));
			} else {
				this.$arrowRight.on('click.homeSlider', preventClick).on('touchend.homeSlider', $.proxy(this.nextSlide, this));
				this.$arrowLeft.on('click.homeSlider', preventClick).on('touchend.homeSlider', $.proxy(this.prevSlide, this));
			}
			this.$ul = this.$el.find('ul');
			this.$li = this.$ul.find('li');
			this.$img = this.$li.find('img');
			if (this.$img.width() === 0 || this.$img.height() === 0) {
				this.$img.one('load readystatechange', function () {
					onImagesReady();
				});
			} else {
				onImagesReady();
			}
			/*
			if (Modernizr.touch) {
				that.setupTouch();
			}
			*/
			this.$el.addClass('ready');
			this.setupResize();
		},
		nextSlide: function (e) {
			if (e !== undefined) {
				e.preventDefault();
			}
			if (this.currentIndex >= this.$li.length - 1) {
				return false;
			}
			this.currentIndex += 1;
			this.goToSlide();
		},
		prevSlide: function (e) {
			if (e !== undefined) {
				e.preventDefault();
			}
			if (this.currentIndex <= 0) {
				return false;
			}
			this.currentIndex -= 1;
			this.goToSlide();
		},
		setupResize: function () {
			var that = this;
			if (typeof $.fn.smartResize === 'function') {
				$(window).smartResize({
					callback: $.proxy(that.handleResize, that),
					namespace: 'homeSlider'
				});
				return true;
			}
			setTimeout($.proxy(that.setupResize, that), 100);
		},
		handleResize: function () {
			var i = 0;
			this.$el.height(this.$li.width() / this.ratio);
			this.$li.each(function () {
				$(this).css('left', (i * 100) + '%');
				i += 1;
			});
		},
		setupTouch: function () {
			var that = this,
				//thumbMoveX = 0,
				moveX = 0,
				//thumbCurrentX = 0,
				currentX = 0,
				//thumbStartPos = 0,
				startPos = 0,
				wWidth = that.$el.width(),
				mainTouchStart = function (e) {
					startPos = that.currentIndex * -wWidth;
					moveX = 0;
					currentX = e.originalEvent.touches[0].pageX;
					that.$ul[0].style[Modernizr.prefixed('transition')] = '-webkit-transform 0s ease-out';
				},
				mainTouchEnd = function () {
					if (moveX !== 0) {
						//move to the nearest block...
						that.currentIndex = -Math.round((startPos + moveX) / wWidth);
						that.goToSlide();
						return true;
					}
				},
				mainTouchMove = function (e) {
					moveX = e.originalEvent.touches[0].pageX - currentX;
					that.$ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + (startPos + moveX) + 'px, 0)';
				};
			this.$ul
				.off('touchstart.imageTheater touchmove.imageTheater touchend.imageTheater')
				.on('touchstart.imageTheater', mainTouchStart)
				.on('touchmove.imageTheater', mainTouchMove)
				.on('touchend.imageTheater', mainTouchEnd);
		},
		goToSlide: function (i) {
			if (i === undefined) {
				i = this.currentIndex;
			}
			if (i > this.$li.length - 1) {
				i = this.$li.length - 1;
			}
			if (i < 0) {
				i = 0;
			}
			if (Modernizr.csstransforms) {
				this.$ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + -i * 100 + '%, 0)';
			} else {
				this.$ul[0].style.left = (-i * 100) + '%';
			}
			if (i === 0) {
				this.$arrowLeft.addClass('hidden');
			} else {
				this.$arrowLeft.removeClass('hidden');
			}
			if (i === this.$li.length - 1) {
				this.$arrowRight.addClass('hidden');
			} else {
				this.$arrowRight.removeClass('hidden');
			}
			this.currentIndex = i;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new RiotHomeSlider(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		var initHomeSlider = function () {
			$('#home-slider').riotHomeSlider();
		};
		$('body').on('pageLoaded.home', function (e, u) {
			if (u === 'home' && $('.content-outer').hasClass('front-page')) {
				initHomeSlider();
			}
		});
		initHomeSlider();
	});
}(this.jQuery));