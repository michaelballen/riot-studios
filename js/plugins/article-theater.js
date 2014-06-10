//Article Theater 0.0
/*global RiotAPI, Modernizr*/
define(['jquery', 'plugins/riot-api', 'vendor/bootstrap/bootstrap-transition'], function ($) {
	"use strict";
	//definitions
	var $body,
		$html,
		pluginName = 'articleTheater',
		//===Class
		ArticleTheater = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, options);
			this.init();
		};
	ArticleTheater.prototype = {
		init: function () {
			var that = this;
			if (this.$el.data('preload')) {
				this.defaultURL = $body.data('rooturl');
				this.defaultTitle = 'Riot Studios';
				this.shown = true;
			} else {
				this.shown = false;
			}
			this.cache = {};
			this.currentID = 0;
			this.$pageLoading = $('#page-loading');
			this.$header = that.$el.find('header');
			this.$section = that.$el.find('section');
			this.$title = that.$header.find('h1');
			this.$author = that.$header.find('.author-con h2');
			this.$authorThumbnail = that.$header.find('.author-thumb');
			this.$postDate = that.$header.find('.post-date span');
			this.scrolling = false;
			if (Modernizr.touch) {
				$('body')
					.on('click.articleTheater', '[data-article_theater]', function (e) {
						e.preventDefault();
						return false;
					})
					.on('touchmove.articleTheater', '[data-article_theater]', function () {
						that.scrolling = true;
					})
					.on('touchend.articleTheater.close', '[data-article_theater_close]', $.proxy(this.hide, this))
					.on('touchend.articleTheater.open', '[data-article_theater]', function (e) {
						var $this = $(this),
							$infoDiv = $this.parents('.info-con'),
							$article;
						e.preventDefault();
						if (that.scrolling === true) {
							that.scrolling = false;
							return false;
						}
						if ($infoDiv.length) {
							$article = $('#article-' + $infoDiv.data('id'));
						} else {
							$article = $this.parents('article');
						}
						that.getContentAndShow($article.data('id'));
					});
			} else {
				$('body').on('click.articleTheater.open', '[data-article_theater]', function (e) {
					var $this = $(this),
						$infoDiv = $this.parents('.info-con'),
						$article;
					e.preventDefault();
					if ($infoDiv.length) {
						$article = $('#article-' + $infoDiv.data('id'));
					} else {
						$article = $this.parents('article');
					}
					that.getContentAndShow($article.data('id'));
				}).on('click.articleTheater.close', '[data-article_theater_close]', $.proxy(this.hide, this));
			}
		},
		show: function () {
			var that = this,
				$el = that.$el,
				$contentOuter,
				$currentObj = this.cache[this.currentID];
			if (this.shown) {
				return false;
			}
			this.shown = true;
			$contentOuter = $('.content-outer');
			$el.addClass('on');
			Modernizr.csstransitions && $contentOuter.addClass('trans');
			$contentOuter[0].offsetWidth;//reflow
			//add article theater class to html el
			$html.addClass('article-theater');
			$('.post.previewing').removeClass('previewing');
			$('.info-con.previewing').removeClass('previewing');
			$el[0].offsetWidth;//force reflow
			$el.addClass('shown');
			//catch body ajax load event to hide article
			$body.trigger('scrollCheck').one('pageLoading.articleTheater', $.proxy(that.hide, that));
			if (Modernizr.history) {
				this.defaultTitle = document.title;
				this.defaultURL = window.location.href;
				document.title = $currentObj.post_title + ' // Riot Studios';
				history.pushState({}, '', $currentObj.permalink);
			}
		},
		hide: function (e) {
			var that = this,
				$el = that.$el;
			if (!this.shown) {
				return false;
			}
			if (typeof e === 'object') {
				e.preventDefault();
			}
			this.shown = false;
			$el.removeClass('shown');
			if (Modernizr.csstransitions) {
				$('content-outer').one($.support.transition.end, function () {
					$(this).removeClass('trans');
				});
			}
			$html.removeClass('article-theater');
			if ($.support.transition) {
				$el.one($.support.transition.end, function () {
					$el.removeClass('on');
				});
			} else {
				$el.removeClass('on');
			}
			$body.off('pageLoading.articleTheater');
			if (Modernizr.history) {
				document.title = this.defaultTitle;
				history.pushState({}, '', this.defaultURL);
			}
		},
		toggle: function () {
			var that = this;
			that.shown? that.hide() : that.show();
		},
		getContentAndShow: function (id) {
			var that = this;
			//are other theaters already open?
			if (this.$el.hasClass('shown') || $html.hasClass('video-theater') || $html.hasClass('image-theater')) {
				return false;
			}
			//check to see if content is already set...
			if (this.currentID === id) {
				that.show();
				return true;
			}
			this._showPageLoad();
			//check the cache to see if we already have this post...
			if (typeof this.cache[id] === 'object') {
				that.currentID = id;
				that.setContent(this.cache[id]);
				that._hidePageLoad();
				that.show();
				return true;
			}
			RiotAPI.call({
				data: {
					action: 'getArticle',
					id: id
				},
				after: function (msg) {
					that.cache[id] = msg;
					that.currentID = id;
					that.setContent(msg);
					that._hidePageLoad();
					that.show();
				}
			});
		},
		setContent: function (o) {
			var that = this;
			that.$title.html(o.post_title);
			if (o.author_thumbnail) {
				that.$authorThumbnail.html(o.author_thumbnail);
			} else {
				that.$authorThumbnail.addClass('hidden');
			}
			console.log(o.post_date);
			console.log(that.$postDate);
			that.$postDate.html(o.post_date);
			that.$author.html(o.post_author_name);
			that.$section.html(o.post_content);
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
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new ArticleTheater(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$body = $('body');
		$html = $('html');
		$('#article-theater').articleTheater();
	});
});