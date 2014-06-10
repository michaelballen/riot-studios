//Select Dropdown 0.0
//extends twitter bootstrap dropdown
define(['jquery', 'vendor/bootstrap/bootstrap-dropdown'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'selectDropdown',
		//===Class
		SelectDropdown = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(),  options);
			this.init();
		};
	SelectDropdown.prototype = {
		init: function () {
			var that = this;
			this.$title = this.$el.find('[data-select_dropdown_title]');
			this.$options = this.$el.find('.dropdown-menu a').on('click.selectDropdown', function (e) {
				that.$title.html($(this).html());
				e.preventDefault();
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
				$this.data(pluginName, (data = new SelectDropdown(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$('[data-select_dropdown]').selectDropdown();
	});
});