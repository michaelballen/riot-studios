//Image Theater 0.0
/*global RiotAPI, Modernizr*/
define(['jquery'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'imageTheater',
		$window,
		$body,
		$html,
		//===Class
		ImageTheater = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	ImageTheater.prototype = {
		init: function () {
			var that = this;
			$window = $(window);
			this.cache = {};
			this.currentID = 0;
			that.$twitterIntent = $('#image-slideshow-twitter-share');
			that.$ul = that.$el.find('.image-slideshow ul');
			that.$thumb_con = that.$el.find('.image-thumbs');
			that.$thumb_ul = that.$thumb_con.find('ul');
			that.$thumb_next = that.$el.find('a[data-image_thumbs_next]');
			that.$thumb_prev = that.$el.find('a[data-image_thumbs_prev]');
			that.$main_prev = that.$el.find('a[data-image_full_prev]');
			that.$main_next = that.$el.find('a[data-image_full_next]');
			that.$captionContainer = that.$el.find('.info-con .image-caption');
			that._setupClicks();
			if (this.$el.data('preload')) {
				this.preload = true;
				if (typeof RiotAPI === 'object') {
					this.loadSlideshow(this.$el.data('preload'));
				} else {
					$(window).one('apiReady.imageTheater', function () {
						that.loadSlideshow(that.$el.data('preload'));
					});
				}
			}
		},
		show: function () {
			var that = this,
				$currentObj;
			that.$el.removeClass('thumbs-on caption-on');
			if ($.support.transition) {
				$('.content-outer').addClass('trans');
				that.$el.addClass('on');
				that.$el[0].offsetWidth;
				that.$el.addClass('shown');
			} else {
				that.$el.addClass('on shown');
			}
			$html.addClass('image-theater');
			that.setupControls();
			$('.info-con.previewing, .post.previewing').removeClass('previewing');
			$body.removeClass('page-loading').one('pageLoading.imageTheater', $.proxy(this.hide, this));
			if (Modernizr.history) {
				if (this.preload) {
					this.defaultURL = RiotAPI.rootURL;
					this.defaultTitle = document.title;
					this.preload = false;
				} else {
					this.defaultURL = window.location.href;
					this.defaultTitle = document.title;
				}
				$currentObj = this.cache[this.currentID];
				history.pushState({}, '', $currentObj.permalink);
				document.title = $currentObj.title + ' // Riot Studios';
			}
		},
		hide: function () {
			var that = this;
			if ($.support.transition) {
				$('.content-outer').one($.support.transition.end, function () {
					$(this).removeClass('trans');
				});
				that.$el.removeClass('shown controls-on').one($.support.transition.end, function () {
					that.$el.removeClass('on');
				});
			} else {
				that.$el.removeClass('shown controls-on on');
			}
			$html.removeClass('image-theater');
			$window.off('keyup.imageTheater.arrows');
			$body.off('pageLoading.imageTheater');
			if (Modernizr.history && this.defaultURL) {
				document.title = this.defaultTitle;
				history.pushState({}, '', this.defaultURL);
			}
		},
		toggle: function () {
			if (this.$el.hasClass('shown')) {
				return this.hide();
			}
			return this.show();
		},
		placeImages: function (data) {
			var that = this,
				size,
				i = 0,
				x,
				current_img,
				$new_li,
				$new_thumb,
				single_thumb_width,
				$images = data.images,
				newProgressBullet;
			that.thumb_ul_width = 0;
			size = that._getMediaSize();
			that.$ul.html('');
			that.$thumb_ul.html('');
			if(!Modernizr.touch) {
				this.$progressBullets = this.$el.find('.progress-bullets').html('');
			}
			for (x in $images) {
				current_img = $images[x];
				$new_li = $("<li data-index=" + i + "><span></span><img src=\"" + current_img.full + "\" /></li>");
				$new_li.css('left', (i * 100).toString() + "%");
				$new_thumb = $("<li data-photoref=" + i + "><img src=\"" + current_img.thumbnail + "\" /></li>");
				that.$ul.append($new_li);
				that.$thumb_ul.append($new_thumb);
				if (!single_thumb_width) {
					single_thumb_width = $new_thumb.outerWidth(true);
				}
				that.thumb_ul_width += single_thumb_width;
				if (!Modernizr.touch) {
					if (i === 0) {
						newProgressBullet = '<a data-photoref=0 class="on" />';
					} else {
						newProgressBullet = '<a data-photoref=' + i + ' />';
					}
					this.$progressBullets.append(newProgressBullet);
				}
				i += 1;
			}
			that.$thumb_ul.width(that.thumb_ul_width);
			if (data.content === "") {
				//empty content, don't show i block
				that.$el.addClass('no-caption');
			} else {
				that.$el.removeClass('no-caption');
				that.$captionContainer.html(data.content);
			}
			that.$twitterIntent.attr('href', that._buildTwitterURL({
				url: data.permalink,
				text: data.title + ' - images from #riotstudios',
				via: 'riotstudios'
			}));
			if (i === 1) {
				this.$el.addClass('one-image');
			} else {
				this.$el.removeClass('one-image');
			}
			if (that.thumb_ul_width <= that.$el.width()) {
				this.$el.find('.thumb-arrow').addClass('hidden');
			} else {
				this.$el.find('.thumb-arrow').removeClass('hidden');
			}
			that.$li = that.$ul.find('li');
			that.images = $images;
			that.currentIndex = 0;
			that.goToImage();
		},
		loadSlideshow: function (id) {
			var that = this,
				data,
				loadAndShow = function () {
					that.placeImages(data);
					that.currentID = id;
					return that.show();
				};
			//make sure other theaters aren't open
			if (!this.preload && (this.$el.hasClass('shown') || $html.hasClass('video-theater') || $html.hasClass('article-theater'))) {
				return false;
			}
			//make sure this gallery isn't already shown
			if (id === this.currentID) {
				this.show();
				return false;
			}
			$body.addClass('page-loading');
			//do we have the image data cached?
			if (this.cache.hasOwnProperty(id)) {
				data = this.cache[id];
				return loadAndShow();
			}
			//if we don't have the data cached, use the api to look it up...
			RiotAPI.call({
				data: {
					action: 'getImageGallery',
					id: id
				},
				after: function (msg) {
					that.cache[id] = msg;
					data = msg;
					loadAndShow();
				}
			});
		},
		setupControls: function () {
			var that = this;
			if (Modernizr.touch) {
				that._setupTouchControls();
			} else {
				that._setupClickControls();
			}
		},
		nextImage: function (e) {
			var that = this;
			if (e !== undefined) {
				e.preventDefault();
			}
			if (that.currentIndex >= that.$li.length - 1) {
				return false;
			}
			that.currentIndex += 1;
			that.goToImage();
		},
		prevImage: function (e) {
			var that = this;
			if (e !== undefined) {
				e.preventDefault();
			}
			if (that.currentIndex <= 0) {
				return false;
			}
			that.currentIndex -= 1;
			that.goToImage();
		},
		goToImage: function (i) {
			var wWidth = this.$el.width(),
				goToX,
				cssHyphen;
			if (i === undefined) {
				i = this.currentIndex;
			}
			//limits for touch
			if (Modernizr.touch) {
				if (i < 0) {
					i = 0;
				} else if (i > this.$li.length - 1) {
					i = this.$li.length - 1;
				}
			} else {
				if (i === 0) {
					this.$main_prev.addClass('hidden');
					this.$main_next.removeClass('hidden');
				} else if (i === this.$li.length - 1) {
					this.$main_prev.removeClass('hidden');
					this.$main_next.addClass('hidden');
				} else if (i < 0 || i > this.$li.length - 1) {
					return false;
				} else {
					this.$main_prev.removeClass('hidden');
					this.$main_next.removeClass('hidden');
				}
			}
			if (Modernizr.touch) {
				goToX = -wWidth * i;
				cssHyphen = Modernizr.prefixed('transform').replace(/([A-Z])/g, function(str,m1){ return '-' + m1.toLowerCase(); }).replace(/^ms-/,'-ms-');
				this.$ul[0].style[Modernizr.prefixed('transition')] = cssHyphen + ' 0.3s ease-out';
				this.$ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + goToX + 'px, 0)';
			} else {
				if (Modernizr.csstransforms) {
					this.$ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + -i * 100 + '%, 0)';
				} else {
					this.$ul[0].style.left = (-i * 100) + '%';
				}
				//turn on the correct progress bullet
				this.$progressBullets.find('.on').removeClass('on');
				this.$progressBullets.find('[data-photoref=' + i +']').addClass('on');
			}
			this.currentIndex = i;
		},
		handleThumbClick: function (e) {
			var that = this,
				$target = $(e.target),
				$li = $target.parents('li[data-photoref]');
			if (!$li.length) {
				return false;
			}
			that.goToImage($li.data('photoref'));
		},
		toggleThumbs: function (e) {
			this.$el.removeClass('caption-on').toggleClass('thumbs-on');
			e.preventDefault();
		},
		toggleCaption: function (e) {
			this.$el.removeClass('thumbs-on').toggleClass('caption-on');
			e.preventDefault();
		},
		closeInfoCon: function (e) {
			this.$el.removeClass('thumbs-on caption-on');
			e.preventDefault();
		},
		toggleControls: function () {
			var that = this;
			that.$el.toggleClass('controls-on');
		},
		_showPageLoad: function () {
			console.log('show page load');
			$body.addClass('page-loading');
			this.$pageLoading[0].offsetWidth;
			this.$pageLoading.addClass('on');
		},
		_hidePageLoad: function () {
			if (this.$pageLoading.hasClass('on') && Modernizr.csstransitions) {
				console.log('hide page load');
				this.$pageLoading.one($.support.transition.end, function () {
					$body.removeClass('page-loading');
					console.log('pageloading');
				});
				this.$pageLoading[0].offsetWidth;
				this.$pageLoading.removeClass('on');
				return true;
			}
			this.$pageLoading.removeClass('on');
			$body.removeClass('page-loading');
		},
		_setupTouchControls: function () {
			var that = this,
				thumbMoveX = 0,
				moveX = 0,
				thumbCurrentX = 0,
				currentX = 0,
				thumbStartPos = 0,
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
						that.goToImage();
						return true;
					}
					that.toggleControls();
				},
				mainTouchMove = function (e) {
					moveX = e.originalEvent.touches[0].pageX - currentX;
					that.$ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + (startPos + moveX) + 'px, 0)';
				},
				thumbTouchStart = function (e) {
					thumbCurrentX = e.originalEvent.touches[0].pageX;
					that.$thumb_ul[0].style[Modernizr.prefixed('transition')] = '-webkit-transform 0s ease-out';
				},
				thumbTouchEnd = function (e) {
					var currentPos,
						$target;
					if (thumbMoveX !== 0) {
						//limits
						currentPos = thumbStartPos + thumbMoveX;
						if (currentPos > 0) {
							currentPos = 0;
						} else if (currentPos < (-that.thumb_ul_width + wWidth)) {
							currentPos = wWidth - that.thumb_ul_width;
						}
						that.$thumb_ul[0].style[Modernizr.prefixed('transition')] = '-webkit-transform 0.3s ease-out';
						that.$thumb_ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + currentPos + 'px, 0)';
						thumbMoveX = 0;
						thumbStartPos = currentPos;
						return true;
					}
					//scroll to the image that we just tapped
					$target = e.target.tagName.toUpperCase() === 'LI' ? $(e.target) : $(e.target).parents('li');
					that.goToImage($target.data('photoref'));
				},
				thumbTouchMove = function (e) {
					thumbMoveX = e.originalEvent.touches[0].pageX - thumbCurrentX;
					that.$thumb_ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + (thumbStartPos + thumbMoveX) + 'px, 0)';
				};
			this.$ul
				.off('touchstart.imageTheater touchmove.imageTheater touchend.imageTheater')
				.on('touchstart.imageTheater', mainTouchStart)
				.on('touchmove.imageTheater', mainTouchMove)
				.on('touchend.imageTheater', mainTouchEnd);
			this.$thumb_ul
				.off('touchstart.imageTheater touchmove.imageTheater touchend.imageTheater')
				.on('touchstart.imageTheater', thumbTouchStart)
				.on('touchmove.imageTheater', thumbTouchMove)
				.on('touchend.imageTheater', thumbTouchEnd);
		},
		_setupClickControls: function () {
			var that = this;
			that.$ul.off('click.imageTheater').on('click.imageTheater', $.proxy(that.nextImage, that));
			//get image thumb click to work
			that.$el.find('[data-photoref]').on('click.thumbClick', function (e) {
				var $this = $(this);
				e.preventDefault();
				that.goToImage($this.data('photoref'));
			});
			$('body').off('keyup.imageTheater.arrows').on('keyup.imageTheater.arrows', function (e) {
				var w = e.which;
				if (w === 39) {
					return that.nextImage();
				}
				if (w === 37) {
					return that.prevImage();
				}
				return false;
			});
			that.$main_prev.off('click.imageTheater').on('click.imageTheater', $.proxy(that.prevImage, that));
			that.$main_next.off('click.imageTheater').on('click.imageTheater', $.proxy(that.nextImage, that));
		},
		_getMediaSize: function () {
			this.window_width = $window.width();
			if ($window.height() > 640 || this.window_width > 640) {
				return 'large';
			}
			return 'medium';
		},
		_setupClicks: function () {
			var that = this;
			if (Modernizr.touch) {
				$body.on('click.imageTheater.loader', '[data-image_theater]', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.imageTheater.loader', '[data-image_theater]', function (e) {
					var id = $(this).data('image_theater');
					e.preventDefault();
					return that.loadSlideshow(id);
				}).on('click.imageTheater.close', '[data-image_theater_close]', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.imageTheater.close', '[data-image_theater_close]', function (e) {
					e.preventDefault();
					return that.hide();
				});
			} else {
				$body.on('click.imageTheater', '[data-image_theater]', function (e) {
					var id = $(this).data('image_theater');
					e.preventDefault();
					return that.loadSlideshow(id);
				}).on('click.imageTheater', '[data-image_theater_close]', function (e) {
					e.preventDefault();
					return that.hide();
				})
				.on('click.imageTheater', '[data-image_theater_thumbs]', $.proxy(this.toggleThumbs, this))
				.on('click.imageTheater', '[data-image_theater_caption]', $.proxy(this.toggleCaption, this))
				.on('click.imageTheater', '[data-image_theater_close_info]', $.proxy(this.closeInfoCon, this));
			}
		},
		_buildTwitterURL: function (o) {
			var u = 'http://twitter.com/intent/tweet?',
				arr = [],
				x;
			for (x in o) {
				arr.push(x + '=' + encodeURIComponent(o[x]));
			}
			u += arr.join('&');
			return u;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new ImageTheater(this, options)));
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
		$html = $('html');
		$('#image-theater').imageTheater();
	});
}(this.jQuery));