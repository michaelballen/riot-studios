//Riot Modal 0.0
/*global Modernizr*/
define(['jquery', 'vendor/bootstrap/bootstrap-transition', ''], function ($) {
	"use strict";
	//definitions
	var pluginName = 'riotModal',
		//===Class
		RiotModal = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({
				top:200
			}, this.$el.data(),  options);
			this.init();
		};
	RiotModal.prototype = {
		init: function () {
			var that = this;
			this.shown = false;
			this.$modal = $('#riot-modal');
			if (!this.$modal.length) {
				$('body').append('<div id="riot-modal"><div id="riot-modal-background" /><div id="riot-modal-content" class="riot-modal-content"><a data-closeModal class="close-btn" href="#">&times;</a><div class="content-inner"></div></div>');
				this.$modal = $('#riot-modal');
			}
			this.$background = this.$modal.find('#riot-modal-background').on('click.riotModal', $.proxy(this.hide, this));
			this.$content = this.$modal.find('#riot-modal-content');
			this.$contentInner = this.$content.find('.content-inner');
			this.id = this.$el[0].id;
			$('[href="#' + this.id + '"]').on('click.riotModal', function (e) {
				that.show();
				e.preventDefault();
			});
		},
		show: function () {
			this.$modal.addClass('on');
			this.$contentInner.html(this.$el.html());
			this.$modal[0].offsetWidth;
			this.$content.css({
				marginLeft: -Math.round(this.$contentInner.outerWidth() * 0.5),
				top:this.options.top
			});
			this.$modal.addClass('show');
			$('body').off('click.riotModal.closeModal').one('click.riotModal.closeModal', '[data-closeModal]', $.proxy(this.hide, this));
		},
		hide: function (e) {
			var that = this;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (Modernizr.csstransitions) {
				this.$modal.one($.support.transition.end, function () {
					that.$modal.removeClass('on');
				}).removeClass('show');
				return true;
			}
			this.$modal.removeClass('on show');
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new RiotModal(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		var $generalModal = $('#general-modal');
		$('[data-modal]').riotModal();
		if (!$generalModal.length) {
			$('body').append('<div id="general-modal"><div class="content general-content"><h2 /><p /></div></div>');
			$generalModal = $('#general-modal');
		}
		$generalModal.riotModal();
		$.showModal = function (header, body) {
			$generalModal.find('h2').html(header);
			$generalModal.find('p').html(body);
			$generalModal.riotModal('show');
		};
	});
});