//Video Theater 0.0
/*global YT*/
define(['jquery', 'plugins/riot-api', 'vendor/bootstrap/bootstrap-transition'], function ($) {
	"use strict";
	//definitions
	var $body,
		$html,
		pluginName = 'videoTheater',
		//===Class
		VideoTheater = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	VideoTheater.prototype = {
		init: function () {
			this.loadingVideo = false;
			this.ytLoaded = false;
			if (Modernizr.touch) {
				$('#video-player').replaceWith('<iframe id="video-player" />');
			}
			if (!!navigator.userAgent.match(/firefox/i) || !!navigator.userAgent.match(/MSIE/i)) {
				this.$el.addClass('firefox');
			}
			this.$iframe = $('#video-player');
			this.$pageLoading = $('#page-loading');
			this.$close_btn = this.$el.find('.close-btn');
			this._setupVideoClicks();
			this._checkPreloaded();
		},
		show: function (permalink, title) {
			var that = this,
				$contentOuter = $('.content-outer');
			$('.info-con.previewing, .post.previewing').removeClass('previewing');
			if ($.support.transition) {
				$contentOuter.addClass('trans');
				that.$el.addClass('on');
				that.$el[0].offsetWidth;
				if (this.preload) {
					that.$el.addClass('ready');
					this.preload = false;
				} else {
					that.$el.addClass('shown').one($.support.transition.end, function () {
						$(this).addClass('ready');
						if (that.player) {
							that.player.playVideo();
						}
					});
				}
			} else {
				that.$el.addClass('on shown ready');
				if (that.player) {
					that.player.playVideo();
				}
			}
			$html.addClass('video-theater');
			$body.one('pageLoading.videoTheater', $.proxy(this.stopAndHide, this));
			if (Modernizr.history && permalink !== undefined) {
				this.defaultURL = window.location.href;
				this.defaultTitle = document.title;
				history.pushState({
					riotVideo: permalink
				}, '', permalink);
				if (title !== undefined) {
					document.title = title;
				}
				console.log('mod history');
			}
		},
		loadYoutube: function () {
			var that = this;
			
			window.onYouTubeIframeAPIReady = function () {
				that.player = new YT.Player('video-player', {
					events: {
						onReady: $.proxy(that._onPlayerReady, that),
						onStateChange: $.proxy(that._onPlayerStateChange, that)
					},
					wmode: 'transparent',
					playerVars: {
						controls: 0,
						showinfo: 0,
						modestbranding: 1,
						wmode: "transparent"
					}
				});
			};
			this._loadYTApi();
		},
		hide: function () {
			var that = this,
				$contentOuter = $('.content-outer');
			if ($.support.transition) {
				$contentOuter.one($.support.transition.end, function () {
					$(this).removeClass('trans');
				});
				$html.removeClass('video-theater');
				that.$el.removeClass('ready shown').one($.support.transition.end, function () {
					that.$el.removeClass('on');
				});
			} else {
				$html.removeClass('video-theater');
				that.$el.removeClass('shown on ready');
			}
			$body.off('pageLoading.videoTheater');
			if (Modernizr.history && this.defaultURL !== undefined) {
				history.pushState({
					riotVideo: this.defaultURL
				}, '', this.defaultURL);
				document.title = this.defaultTitle;
			}
		},
		showCloseButton: function () {
			this.$close_btn.addClass('shown');
		},
		hideCloseButton: function () {
			this.$close_btn.removeClass('shown');
		},
		stopAndHide: function () {
			if (this.player) {
				this.player.stopVideo();
			} else {
				this.$iframe.replaceWith('<iframe id="video-player" />');
				this.$iframe = $('#video-player');
			}
			this.hide();
		},
		playVideo: function (id, permalink, title) {
			var that = this,
				cueVideo;
			if (!this.preload && (this.loadingVideo || this.$el.hasClass('shown') || $html.hasClass('image-theater') || $html.hasClass('article-theater'))) {
				return false;
			}
			this.loadingVideo = true;
			this._showPageLoad();
			if (Modernizr.touch) {
				this.$iframe.attr('src', 'http://www.youtube.com/embed/' + id);
				that._hidePageLoad();
				that.show(permalink, title);
				that.loadingVideo = false;
				return id;
			}
			cueVideo = function () {
				that.player.cueVideoById(id);
				that._hidePageLoad();
				that.show(permalink, title);
				that.loadingVideo = false;
			};
			if (!this.ytLoaded) {
				this._onPlayerReady = cueVideo;
				this._loadYTApi();
				return true;
			}
			//youtube's already good to go, just load the vid
			cueVideo();
		},
		_showPageLoad: function () {
			$body.addClass('page-loading');
			this.$pageLoading[0].offsetWidth;
			this.$pageLoading.addClass('on');
		},
		_hidePageLoad: function () {
			if (this.$pageLoading.hasClass('on') && Modernizr.csstransitions) {
				this.$pageLoading.one($.support.transition.end, function () {
					$body.removeClass('page-loading');
				});
				this.$pageLoading[0].offsetWidth;
				this.$pageLoading.removeClass('on');
				return true;
			}
			this.$pageLoading.removeClass('on');
			$body.removeClass('page-loading');
		},
		_onPlayerStateChange: function (e) {
			var that = this;
			if (e.data === 0 || e.data === 2) {
				that.showCloseButton();
			} else {
				that.hideCloseButton();
			}
		},
		_loadYTApi: function () {
			var that = this,
				events = {
					onStateChange: $.proxy(that._onPlayerStateChange, that)
				};
			if (typeof that._onPlayerReady === 'function') {
				events.onReady = $.proxy(that._onPlayerReady, that);
			}
			window.onYouTubeIframeAPIReady = function () {
				that.player = new YT.Player('video-player', {
					events: events,
					wmode: 'transparent',
					playerVars: {
						showinfo: 0,
						modestbranding: 1,
						wmode: "transparent"
					}
				});
			};
			$.ajaxSetup({
				cache: true
			});
			$.getScript("https://www.youtube.com/iframe_api");
			this.ytLoaded = true;
		},
		_setupVideoClicks: function () {
			var that = this;
			if (Modernizr.touch) {
				$body.on('click.videoTheater', '[data-close_video],[data-play_video]', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.videoTheater.play', '[data-play_video]', function (e) {
					var $this = $(this);
					e.preventDefault();
					that.playVideo($this.data('play_video'), $this.attr('href'), $this.attr('title'));
				}).on('touchend.videoTheater.close', '[data-close_video]', function (e) {
					e.preventDefault();
					that.stopAndHide();
				});
			} else {
				$body.on('click.videoTheater.close', '[data-close_video]', function (e) {
					e.preventDefault();
					that.stopAndHide();
				}).on('click.videoTheater.play', '[data-play_video]', function (e) {
					var $this = $(this);
					e.preventDefault();
					that.playVideo($this.data('play_video'), $this.attr('href'), $this.attr('title'));
				});
			}
		},
		_checkPreloaded: function () {
			var id = this.$el.data('preload');
			if (!id) {
				return false;
			}
			this.defaultURL = $body.data('rooturl');
			this.defaultTitle = 'Riot Studios';
			this.preload = true;
			this.playVideo(id);
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option, args) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new VideoTheater(this, options)));
			}
			if (typeof option === 'string') {
				data[option](args);
			}
		});
	};
	$.fn[pluginName].defaults = {
	};
	//===Data-API
	$(function () {
		$html = $('html');
		$body = $('body');
		$('#video-theater').videoTheater();
	});
}(this.jQuery));