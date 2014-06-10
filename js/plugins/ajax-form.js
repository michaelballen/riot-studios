//Ajax Form 0.0
/*global Modernizr, Handlebars*/
define(['jquery', 'vendor/bootstrap/bootstrap-transition', 'vendor/handlebars'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'ajaxForm',
		$body,
		//===Class
		AjaxForm = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(),  options);
			this.init();
		};
	AjaxForm.prototype = {
		init: function () {
			var that = this,
				formSubmit = function (e) {
					var data;
					if (typeof e === 'object' && typeof e.preventDefault === 'function') {
						e.preventDefault();
					}
					if (that.submitting) {
						return false;
					}
					that.showLoading();
					that.submitting = true;
					data = that.$el.serialize();
					$.ajax({
						url: that.action,
						type: that.method,
						data: data,
						dataType: 'json'
					}).done($.proxy(that.showResponse, that));
					return false;
				};
			this.method = this.$el.attr('method') || 'post';
			this.action = this.$el.attr('action');
			this.submitting = false;
			this.$submit = this.$el.find('button[data-submit], a[data-submit]');
			this.$el.on('submit.ajaxForm', formSubmit);
			if (Modernizr.touch) {
				this.$submit.on('touchend.ajaxForm', formSubmit);
			} else {
				this.$submit.on('click.ajaxForm', formSubmit);
			}
		},
		showLoading: function () {
			$('body').addClass('page-loading');
		},
		showErrorMessage: function (msg) {
			$('body').removeClass('page-loading');
			if (Modernizr.touch) {
				alert(msg);
			}
			this.submitting = false;
		},
		showResponse: function (msg) {
			var hbSuccess,
				successTemp;
			if (typeof msg === 'object') {
				if (!msg.success) {
					return this.showErrorMessage(msg.errormsg);
				} else {
					msg = msg.data;
				}
			}
			if (this.options.success_ui) {
				hbSuccess = $('#' + this.options.success_ui).html();
				successTemp = Handlebars.compile(hbSuccess);
				this.$el.html(successTemp({
					msg: msg
				}));
			}
			$('body').removeClass('page-loading');
			this.submitting = false;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new AjaxForm(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//add function to jquery that runs certain things when pages are supposed to be loaded
	//===Data-API
	$(function () {
		$body = $('body');
		$('form[data-ajax_form]').ajaxForm();
	});
});