//Video Player 0.0
/*global RiotAPI, videojs*/
require(['jquery', 'plugins/riot-plugins', 'plugins/riot-api', 'vendor/video-js/video'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'videoPlayer',
		$body,
		//===Class
		VideoPlayer = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(),  options);
			this.init();
		};
		/*
		LIST:
		- install htaccess that prevents file download (check favorites in chrome for article on this)
		- customize skinning a bit (look for what I can do using skins/css in video.js docs)
		- finish styling of page in general and video player
		*/
	VideoPlayer.prototype = {
		init: function () {
			var that = this;
			if (typeof _V_ !== 'function') {
				setTimeout(function () {
					that.init();
				}, 100);
				return false;
			}
			this.playPosition = 0;
			this.videoSetup = false;
			this.videoSource = false;
			this.hasRiotAPI = $('html').hasClass('apiReady');
			this.markAsWatched = this.$el.data('mark_as_watched') !== undefined;
			this.orderID = this.$el.data('order_id');
			this.watchToken = this.$el.data('watch_token');
			this.nonceVal = this.$el.data('nonce');
			that.fullscreen = false;
			that.$video = this.$el.find('video');
			that.$showBtn = $('[data-show_rental]').on('click.playVideo', $.proxy(this.show, this));
			that.$hideBtn = $('[data-close_rental]').on('click.playVideo', $.proxy(this.hide, this));
			that._getVideoSource();
			that.setupVideo();
		},
		setupVideo: function () {
			var that = this;
			videojs.options.flash.swf = $body.data('templateurl') + "/js/vendor/video-js/video-js.swf";
			videojs("rental-player-video").ready(function () {
				that.videoAPI = this;
				that.videoAPI.on('play', $.proxy(that.onPlay, that));
				$(window).on('resize.videoPlayer', $.proxy(that.onResize, that));
				that.onResize();
				if (that.videoSource !== false) {
					that.setVideoSource();
				}
				that.videoSetup = true;
			});
		},
		trackPlayPosition: function () {
			var v = this.videoAPI,
				pos = v.currentTime();
			if (pos !== this.playPosition) {
				this.playPosition = pos;
				RiotAPI.call({
					data: {
						action: 'logVideoPlayPosition',
						play_position: pos,
						order_id: this.orderID,
						watch_token: this.watchToken,
						streaming_nonce: this.nonceVal
					}
				});
			}
			if (v.paused()) {
				v.on("play", $.proxy(this.trackPlayPosition, this));
			} else {
				setTimeout($.proxy(this.trackPlayPosition, this), 30000);
			}
		},
		onPlay: function () {
			var that = this,
				attachApiHandler = function () {
					RiotAPI.call({
						data: {
							action: 'logVideoStarted',
							order_id: that.orderID,
							watch_token: that.watchToken
						}
					});
				};
			that.videoAPI.off("play");
			that.trackPlayPosition();
			if (this.hasRiotAPI) {
				attachApiHandler();
			} else {
				$(window).on('apiReady.videoPlayer.logVideoStart', $.proxy(attachApiHandler, this));
			}
		},
		onResize: function () {
			var $w = $(window),
				w = $w.width(),
				h = $w.height();
			if (w >= 1280 && h >= 720) {
				w = 1280;
				h = 720;
			} else if ((w / h) > (16 / 9)) {
				//screen is wider than necessary, set height first
				w = h * 16 / 9;
			} else {
				h = w * 9 / 16;
			}
			this.videoAPI.width(Math.round(w)).height(Math.round(h));
			this.$el.css('line-height', $(window).height() + 'px');
		},
		show: function (e) {
			var playPosition = this.videoSource.playPosition || 0;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (playPosition) {
				this.videoAPI.currentTime(playPosition);
			}
			this.$el.addClass('on');
			
		},
		hide: function (e) {
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (typeof this.videoAPI === 'object' && typeof this.videoAPI.paused === 'function' && !this.videoAPI.paused()) {
				this.videoAPI.pause();
			}
			this.$el.removeClass('on');
		},
		setVideoSource: function () {
			this.videoAPI.src([
				{
					type: 'video/mp4',
					src: this.videoSource.mp4
				}, {
					type: 'video/webm',
					src: this.videoSource.webm
				}
			]);
			$('.vjs-poster').css({
				backgroundImage: 'url(' + this.videoSource.poster + ')',
				width: '100%',
				height: '100%'
			});
			this.videoAPI.posterImage.show();
		},
		_getVideoSource: function () {
			var that = this;
			if (!this.hasRiotAPI) {
				//if the riot api isn't set up yet, wait til it is then run the function
				$(window).on('apiReady.videoPlayer.getVideoSource', $.proxy(this._getVideoSource, this));
				return false;
			}
			RiotAPI.call({
				data: {
					action: 'getStreamingLinks',
					order_id: this.orderID,
					watch_token: this.watchToken,
					streaming_nonce: this.nonceVal
				},
				after: function (m, status) {
					if (status === 'success') {
						that.videoSource = m;
						if (that.videoSetup === true) {
							that.setVideoSource();
						}
						return true;
					}
					//alert error message if no success
					alert(m);
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
				$this.data(pluginName, (data = new VideoPlayer(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$body = $('body');
		$('#rental-player').videoPlayer();
	});
}(this.jQuery));