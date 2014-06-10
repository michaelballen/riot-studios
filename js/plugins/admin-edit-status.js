//Admin Edit Status 0.0
/*global RiotAPI*/
define(['jquery', 'plugins/riot-api'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'adminEditStatus',
		//===Class
		AdminEditStatus = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(),  options);
			this.init();
		};
	AdminEditStatus.prototype = {
		init: function () {
			this.$parentData = this.$el.parents('[data-admin_edit_data_parent]');
			this.id = this.$el.data('edit_id') || this.$parentData.data('edit_id');
			this.action = this.$el.data('action') || this.$parentData.data('action');
			this.nonceKey = this.$el.data('nonce_key') || this.$parentData.data('nonce_key');
			if (this.nonceKey) {
				this.nonceValue = this.$el.data('nonce_value') || this.$parentData.data('nonce_value');
			}
			this.metaKey = this.$el.data('key');
			this.secretKey = this.$el.data('secret_key') || this.$parentData.data('secret_key');
			if (this.secretKey) {
				this.secretValue = this.$el.data('secret_value') || this.$parentData.data('secret_value');
			}
			this.idKey = this.$el.data('id_key') || this.$parentData.data('id_key') || 'id';
			this.type = this._getType();
		},
		setupToggle: function () {
			var that = this;
			this.onValue = this.$el.data('on');
			this.offValue = this.$el.data('off');
			this.toggleClass = this.$el.data('toggle_class');
			this.value = this.$el.data('value');
			this.$el.on('click.adminEditStatus', function (e) {
				that.$el.toggleClass(that.toggleClass);
				if (that.value === that.onValue) {
					that.value = that.offValue;
				} else {
					that.value = that.onValue;
				}
				that._makeApiEdit();
				e.preventDefault();
			});
		},
		_makeApiEdit: function () {
			var $data = {
					action: this.action
				};
				$data[this.idKey] = this.id;
				$data[this.metaKey] = this.value;
				if (this.nonceKey) {
					$data[this.nonceKey] = this.nonceValue;
				}
				if (this.secretKey) {
					$data[this.secretKey] = this.secretValue;
				}
			RiotAPI.call({
				data: $data,
				after: function (m) {
					console.log(m);
				}
			});
		},
		_getType: function () {
			if (this.$el.data('on') !== undefined && this.$el.data('off') !== undefined) {
				this.setupToggle();
				return 'toggle';
			}
			return false;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new AdminEditStatus(this, options)));
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
		$('[data-admin_edit_status]').adminEditStatus();
	});
}(this.jQuery));