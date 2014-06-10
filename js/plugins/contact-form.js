//contact form 0.0
/*global RiotAPI*/
define(['jquery', 'plugins/riot-modal', 'plugins/riot-api', 'vendor/jquery.validate.min'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'riotContactForm',
		//===Class
		RiotContactForm = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({},this.$el.data(),options);
			this.init();
		};
	RiotContactForm.prototype = {
		init: function () {
			this.registerOnly = this.$el.attr('data-register_only') !== undefined;
			this.$userEmailInput = this.$el.find('input[name="user_email"]');
			this.$userMessageInput = this.$el.find('[name="user_message"]');
			this.$nonce = this.$el.find('[name="contact_nonce"]');
			this.$emailRegister = this.$el.find('[name="user_email_register"]');
			this.$submitButton = this.$el.find('[data-submit]').on('click.contactForm', $.proxy(this.send, this));
			this.$el.on('submit.contactForm', $.proxy(this.send, this));
			this._setupModal();
			this._setupValidator();
		},
		send: function (e) {
			var that = this,
				email = this.$userEmailInput.val(),
				message = this.$userMessageInput.val(),
				nonce = this.$nonce.val(),
				emailRegister = (this.$emailRegister.length > 0 && this.$emailRegister.is(':checked')) ? '1' : '0',
				submitHTML = this.$submitButton.html();
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (that.validator) {
				if (that.validator.form() !== true) {
					return false;
				}
			}
			//disable the submit button
			this.$submitButton.attr('disabled', 1).removeClass('btn-primary').addClass('btn-warning').html('Sending &hellip;');
			if (this.registerOnly) {
				RiotAPI.call({
					data: {
						action: 'registerUser',
						user_email: email
					},
					after: function () {
						that.$userEmailInput.val('');
						that._prepModal('Got It!', 'Thanks for signing up!');
						that.$modal.riotModal('show');
						that.$submitButton.removeAttr('disabled').addClass('btn-primary').removeClass('btn-warning').html(submitHTML);
					}
				});
			} else {
				RiotAPI.call({
					data: {
						action: 'contactForm',
						user_email: email,
						message: message,
						contact_nonce: nonce,
						email_register: emailRegister
					},
					after: function (msg) {
						if (msg === 'sent') {
							that.$el.find('input[name="user_name"]').val('');
							that.$userEmailInput.val('');
							that.$userMessageInput.val('');
							that._prepModal('Got It!', 'Thanks for your message.');
							that.$modal.riotModal('show');
							that.$submitButton.removeAttr('disabled').addClass('btn-primary').removeClass('btn-warning').html(submitHTML);
						} else {
							that._prepModal('Hold Up!', 'Go ahead and fix the errors in red before sending.');
							that.$modal.riotModal('show');
							that.$submitButton.removeAttr('disabled').addClass('btn-primary').removeClass('btn-warning').html(submitHTML);
						}
					}
				});
			}
		},
		_prepModal: function (header, msg) {
			this.$modal.find('h2').html(header);
			this.$modal.find('p').html(msg);
		},
		_setupModal: function () {
			var that = this,
				message = this.registerOnly ? 'Thanks for signing up!' : 'Thanks for your message.',
				tryRiotModal = function () {
					if (typeof $.fn.riotModal === 'function' && !that.$modal.data('riotModal')) {
						return that.$modal.riotModal();
					}
					//if it's not setup yet, try again in a second
					setTimeout(tryRiotModal, 1000);
				};
			this.$modal = $('#contact-form-success');
			if (!this.$modal.length) {
				$('body').append('<div id="contact-form-success" class="hidden"><h2>Got It!</h2><p>' + message + '</p></div>');
				this.$modal = $('#contact-form-success');
			}
			tryRiotModal();
		},
		_setupValidator: function () {
			var that = this,
				tryValidator = function () {
					if (typeof $.fn.validate === 'function') {
						that.validator = that.$el.validate({
							rules: {
								user_email: {
									email: true,
									required: true
								},
								user_message: {
									required: true
								}
							}
						});
						return true;
					}
					//if it's not setup yet, try again in a second
					setTimeout(tryValidator, 1000);
				};
			tryValidator();
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (data) {
				$this.data(pluginName, null);
			}
			$this.data(pluginName, (data = new RiotContactForm(this, options)));
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$('form[data-contact_form]').riotContactForm();
	});
}(this.jQuery));