//Ajax Loader 0.0
/*global Modernizr, _gaq*/
require(['jquery', 'plugins/riot-plugins', 'plugins/riot-api', 'vendor/bootstrap/bootstrap-transition'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'ajaxLoader',
		$body,
		//===Class
		AjaxLoader = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(),  options);
			this.init();
		};
	AjaxLoader.prototype = {
		init: function () {
			var that = this;
			this.$scrollTarget = $('html, body');
			this.loading = false;
			this.$mainLoadingDiv = $('#page-loading');
			this.currentPage = window.location.href;
			this.baseURL = this.$el.data('rooturl').replace(/\/$/, '') + "/";
			this.setupClicks();
			window.onpopstate = function () {
				var p = document.location.href;
				if (that.formatPageURL(p) !== that.formatPageURL(that.currentPage)) {
					that.loadPage(p);
				}
			};
		},
		parseURL: function (url) {
			var reg = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^#]*))?(?:#(.*))?$/,
				result_arr = ['url', 'scheme', 'slash', 'host', 'port', 'path', 'query', 'hash'],
				i,
				result = reg.exec(url),
				o = {};
			for (i = 0; i < result_arr.length; i += 1) {
				o[result_arr[i]] = result[i];
			}
			return o;
		},
		testURLMatch: function (url1, url2) {
			if (!url2) {
				url2 = this.currentPage;
			}
			url1 = this.formatPageURL(url1);
			url2 = this.formatPageURL(url2);
			return url1 === url2;
		},
		formatPageURL: function (url) {
			var url_obj,
				formatted = "";
			//if we don't give the url, assume the current page url
			if (url === undefined) {
				url = window.location.href;
			}
			//if it's blank, assume site home page...
			if (url === "") {
				url = this.baseURL;
			}
			url_obj = this.parseURL(url);
			if (!url_obj.scheme || !url_obj.slash || !url_obj.path) {
				formatted = this.baseURL + url.replace(/\/$/, '');
			} else {
				formatted = this.baseURL + url_obj.path.replace(/\/$/, '');
			}
			return formatted;
		},
		getBaseURL: function () {
			if (this.url) {
				return this.url;
			}
			this.url = $('body').data('rooturl') + "/";
			return this.url;
		},
		getPageTitle: function (url) {
			var url_obj,
				title;
			url_obj = this.parseURL(url);
			if (!url_obj.path) {
				url_obj.path = 'Home';
			}
			title = url_obj.path.capitalize();
			return "Riot Studios / " + title;
		},
		loadPage: function (url, data) {
			var that = this,
				$ajax_settings = {},
				$data_object = {},
				new_url = url,
				onDone = function (msg,status,url) {
					if (status === "success") {
						that.currentPage = new_url;
						that.showPageLoaded(msg, status, url);
					}
				},
				onFail = function () {
					window.location.href = new_url;
				};
			if (this.testURLMatch(url)) {
				return false;
			}
			if (this.loading) {
				this.$el.one('pageTransitioned', function () {
					that.loadPage(url,data);
				});
				return false;
			}
			if (!url) {
				url = this.baseURL;
			}
			if (!data) {
				data = {};
			}
			that.showPageLoading(url);
			//use options to setup ajax request
			$data_object = $.extend({
				ajax: '1'
			}, data);
			$ajax_settings = {
				url: url,
				data: $data_object
			};
			//run ajax request
			$.ajax($ajax_settings).done(onDone).fail(onFail);
		},
		showPageLoading: function (url) {
			var that = this,
				scrollTop;
			if (this.loading) {
				return false;
			}
			this.loading = true;
			scrollTop = that.$scrollTarget.scrollTop();
			that.$el.trigger('pageLoading', url);
			if (scrollTop === 0) {
				that.$mainLoadingDiv.removeClass('on');
				that.$el.addClass('page-loading');
				that.$mainLoadingDiv[0].offsetWidth;
				that.$mainLoadingDiv.addClass('on');
			} else {
				that.$scrollTarget.animate({
					scrollTop: 0
				}, (Math.abs(scrollTop) * 0.5), function () {
					that.$mainLoadingDiv.removeClass('on');
					that.$el.addClass('page-loading');
					that.$mainLoadingDiv[0].offsetWidth;
					that.$mainLoadingDiv.addClass('on');
				});
			}
		},
		showPageLoaded: function (content) {
			var that = this,
				$new_content = $(content).addClass('new-content'),
				$old_content = $('.content-outer').addClass('old-content'),
				new_url = this.currentPage,
				new_title = $new_content.data('title') || this.getPageTitle(new_url),
				new_module = $new_content.data('module');
			//add the new content to the document
			$old_content.before($new_content);
			//check if we need to require any js
			if (new_module) {
				require([new_module]);
			}
			//trigger reflows so the old content is display block and they're absolute position
			$old_content[0].offsetWidth;
			$new_content[0].offsetWidth;
			//stop the loading gif display
			this.$el.addClass('page-loaded').removeClass('page-loading');
			//transition the old in, new out
			$old_content.addClass('off');
			$new_content.addClass('on');
			if (typeof _gaq === 'object') {
				console.log('Google track page view - ' + new_url);
				_gaq.push(['_trackPageview', new_url]);
			}
			$new_content.afterTransition(function () {
				history.pushState({page: "Riot Studios"}, new_title, new_url);
				document.title = new_title;
				$old_content.remove();
				$new_content.removeClass('new-content on');
				that.$el.removeClass('page-loaded').trigger('pageLoaded', new_module);
				that.loading = false;
				that.$el.trigger('pageTransitioned');
			});
		},
		setupClicks: function () {
			var that = this,
				handleClick = function (e) {
					var hr = $(this).attr('href');
					that.loadPage(hr);
					e.preventDefault();
				};
			if (Modernizr.touch) {
				that.$el.on('click.ajaxLoader', '[data-ajax_load]', function (e) {
					e.preventDefault();
				});
				that.$el.on('touchend.ajaxLoader', '[data-ajax_load]', handleClick);
			} else {
				that.$el.on('click.ajaxLoader', '[data-ajax_load]', handleClick);
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
				$this.data(pluginName, (data = new AjaxLoader(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//add function to jquery that runs certain things when pages are supposed to be loaded
	$.moduleBind = function (module, f) {
		if (typeof module !== 'string' || typeof f !== 'function') {
			console.log(['first arg must be a string module', 'second arg must be a function']);
			return false;
		}
		$body.off('pageLoaded.moduleBind.' + module);
		$body.on('pageLoaded.moduleBind.' + module, function (e, m) {
			if (m === module) {
				f();
			}
		});
	};
	//===Data-API
	$(function () {
		$body = $('body');
		$('.history body').ajaxLoader();
	});
});