//Mobile Footer 0.0
/*global Modernizr, RiotAPI*/
require(['jquery', 'plugins/riot-api', 'plugins/riot-plugins'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'mobileFooter',
		//===Class
		MobileFooter = function (element) {
			this.$el = $(element);
			this.init();
		};
	MobileFooter.prototype = {
		init: function () {
			var that = this,
				$body = $('body');
			this.$measureLine = this.$el.find('.measure-line');
			this.$links = this.$el.find('a');
			this._setLinkPositions();
			this.baseURL = $body.data('rooturl') + '/';
			this.currentURL = window.location.href;
			$body.on('pageLoading.mobileFooter', function (e, url) {
				//find the link that matches our url
				that.setLine(url);
			});
			this.$links.on('linkSelected', function () {
				var $this = $(this);
				if (Modernizr.csstransforms3d) {
					//do a translate
					that.$measureLine[0].style[Modernizr.prefixed('transform')] = 'translate(' + $this.data('mobileFooterPosition') + ', 0)';
				} else {
					//set the left prop
					that.$measureLine[0].style.left = $this.data('mobileFooterPosition');
				}
			});
			$(window).smartResize({
				namespace: 'mobileFooter',
				callback: $.proxy(that._setLinkPositions, that)
			});
			this.setLine();
			this.$measureLine.removeClass('hidden').addClass('trans');
		},
		setLine: function (url) {
			var $l = null,
				root = $('body').data('rooturl'),
				regex;
			if (url === this.currentURL) {
				return false;
			}
			if (!url) {
				url = window.location.href;
			}
			if (url === root || url === root + '/') {
				this.$el.find('a.logo').trigger('linkSelected');
				return true;
			}
			regex = new RegExp(RiotAPI.parseURL(url).path.split('/')[0]);
			//find the link that matches
			this.$links.each(function () {
				var $this = $(this);
				if (regex.test($this.attr('href'))) {
					$l = $this;
					return false;
				}
			});
			if (!$l) {
				$l = this.$el.find('a.logo');
			}
			$l.trigger('linkSelected');
			this.currentURL = url;
		},
		_formatPageURL: function (url) {//this function copied from ajax-loader, figure out how to combine (jquery fn?)
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
			url_obj = this._parseURL(url);
			if (!url_obj.scheme || !url_obj.slash || !url_obj.path) {
				formatted = this.baseURL + url.replace(/\/$/, '');
			} else {
				formatted = this.baseURL + url_obj.path.replace(/\/$/, '');
			}
			return formatted;
		},
		_parseURL: function (url) {//also copied from ajax-loader
			var reg = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^#]*))?(?:#(.*))?$/,
				result_arr = ['url', 'scheme', 'slash', 'host', 'port', 'path', 'query', 'hash'],
				i,
				result = reg.exec(url),
				o = {};
			for (i = 0; i < result_arr.length; i += 1) {
				o[result_arr[i]] = result[i];
			}
			if (url.indexOf(this.baseURL !== -1)) {
				o.host = this.baseURL;
				o.path = url.replace(this.baseURL + '/', '').replace(this.baseURL, '');
			}
			return o;
		},
		_setLinkPositions: function (e) {
			var that = this;
			this.fullWidth = this.$el.outerWidth();
			this.$links.each(function () {
				var $this = $(this),
					x;
				if (Modernizr.csstransforms3d) {
					if ($this.hasClass('logo')) {
						$this.data('mobileFooterPosition', (that.fullWidth * 0.5) + 'px');
						return true;
					}
					x = $this.position().left + ($this.outerWidth() * 0.5);
					$this.data('mobileFooterPosition', x + 'px');
				} else {
					if ($this.hasClass('logo')) {
						$this.data('mobileFooterPosition', '50%');
						return true;
					}
					x = ($this.position().left + ($this.outerWidth() * 0.5)) / that.fullWidth * 100;
					$this.data('mobileFooterPosition', x + '%');
				}
			});
			//if a resize event is triggering this, we need to reset the line position with no animation
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				that.$measureLine.removeClass('trans');
				that.setLine();
				that.$measureLine[0].offsetWidth;
				that.$measureLine.addClass('trans');
			}
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName);
			if (!data) {
				$this.data(pluginName, (data = new MobileFooter(this)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$('html.history .nav-menu').mobileFooter();
	});
});