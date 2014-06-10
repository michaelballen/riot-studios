//Image Theater 0.0
/*global RiotAPI*/
require(['jquery', 'vendor/bootstrap/bootstrap-transition', 'plugins/riot-api'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'squareLoad',
		//===Class
		SquareLoad = function (element) {
			this.$el = $(element);
			this.colorStr = '0123456789abcdef';
			this.init();
		};
	SquareLoad.prototype = {
		init: function () {
			var that = this,
				src;
			this.$img = this.$el.find('img');
			if (this.$img.length) {
				this.$img.one('load readystatechange', function () {
					that.$img.removeAttr('style');
					if ($.support.transition) {
						that.$el.addClass('loaded');
						that.$img.one($.support.transition.end, function () {
							that.$el.removeClass('unloaded loaded half');
						});
					} else {
						that.$el.removeClass('unloaded half');
					}
				}).on('error', function () {
					console.log('error loading ' + src);
				});
				this.$img.height(this.$img.width());
				this.$el.addClass('half');
				src = this.$img[0].src;
				this.$img[0].src = '#';
				this.$img[0].src = src;
			}
			if (this.$el.data('color_switch')) {
				this.$el.on('click.colorSwitch', function () {
					that.$el.css('backgroundColor', that.getRandomColor);
				});
				this.sizeFiller();
				$(window).on('resize.colorSwitch', $.proxy(this.sizeFiller, this));
			}
		},
		sizeFiller: function () {
			this.$el.height(this.$el.width()).removeClass('unloaded');
		},
		getRandomColor: function () {
			return '#'+Math.floor(Math.random()*16777215).toString(16);
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function () {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName);
			if (!data) {
				$this.data(pluginName, (data = new SquareLoad(this)));
			}
		});
	};
	//===Data-API
	$(function () {
		var setup = function () {
				var $contentInner = $('.content-inner');
				$contentInner.find('.post.unloaded').squareLoad();
			},
			loadMoreSquares = function (e) {
				var $this = $(this),
					paged = $this.data('paged'),
					type = $this.data('type'),
					$contentInner = $('.content-inner');
				$this.addClass('progress');
				RiotAPI.call({
					data: {
						action: 'loadMoreSquares',
						square_paged: paged,
						number: $this.data('number'),
						type: type
					},
					after: function (m, status) {
						var newScroll;
						if (status === 'success') {
							$this.parent().before(m.posts);
							$this.data('paged', m.paged);
							if (m.maxPages < m.paged) {
								$this.parent().remove();
							} else {
								$this.removeClass('progress');
							}
							setup();
							if (!Modernizr.touch) {
								newScroll = $contentInner.scrollTop() + 50;
								$contentInner.animate({
									scrollTop: newScroll
								});
							}
						}
					}
				});
				e.preventDefault();
			};
		$('body').on('pageLoaded.squareLoad', function (e, module) {
			if (module === 'home') {
				setup();
			}
		}).on('click.loadHomeSquares', '[data-load_home_squares]', loadMoreSquares);
		setup();
	});
}(this.jQuery));