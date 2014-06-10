//Checkout Theater 0.0
/*global Modernizr, RiotAPI, Stripe, iScroll*/
define(['jquery', 'checkout', 'vendor/handlebars', 'vendor/jquery.validate.min', 'vendor/iscroll-lite'], function ($) {
	"use strict";
	//definitions
	var $body,
		pluginName = 'checkoutTheater',
		//===Class
		CheckoutTheater = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, options);
			this.init();
		};
	CheckoutTheater.prototype = {
		init: function () {
			var that = this;
			this.$closeBtn = this.$el.find('[data-close_checkout]');
			this.$content = this.$el.find('.form-container');
			this.$loading = this.$el.find('.loading');
			this.shown = false;
			if (Modernizr.touch) {
				this.$closeBtn.on('click.checkoutTheater', function (e) {
					e.preventDefault();
				});
				$('body')
					.on('click.checkoutTheater', '[data-checkout_theater]', function (e) {
						e.preventDefault();
					})
					.on('touchend.checkoutTheater.show', '[data-checkout_theater]', $.proxy(that.show, that))
					.on('touchend.checkoutTheater.close', '[data-close_checkout]', $.proxy(that.hide, that))
					.on('updateShoppingCart.checkoutTheater', $.proxy(that.updateShipAndTotal, that))
					.on('pageLoading.checkoutTheater', $.proxy(that.hide, that));
			} else {
				$('body')
					.on('click.checkoutTheater.show', '[data-checkout_theater]', function (e) {
						that.show();
						e.preventDefault();
					})
					.on('click.checkoutTheater.close', '[data-close_checkout]', function (e) {
						that.hide();
						e.preventDefault();
					})
					.on('updateShoppingCart.checkoutTheater', $.proxy(that.updateShipAndTotal, that))
					.on('pageLoading.checkoutTheater', $.proxy(that.hide, that));
			}
			if (window.location.href.indexOf($body.data('rooturl') + '/checkout') !== -1) {
				this.show();
			}
		},
		updateShipAndTotal: function () {
			if (this.$shoppingCart) {
				var cart = RiotAPI.getCart(),
					ship = cart.shipping ? cart.shipping.toFixed(2) : '0.00';
				this.$shoppingCart.find('.total-amt').text('$' + cart.total.toFixed(2));
				this.$shoppingCart.find('.ship-amt').text('$' + ship);
			}
		},
		onPaymentSuccess: function (o) {
			var x;
			//clear the items from the cart
			RiotAPI.clearCartItems();
			//were there any streaming movies?
			for (x in o.cart) {
				if (o.cart.hasOwnProperty(x) && o.cart[x].hasOwnProperty('watch_token')) {
					this.$paymentSuccess.find('a.receipt-btn').before('<a href="' + RiotAPI.rootURL + '/theater?wT=' + o.cart[x].watch_token + '&oI=' + o.id + '" class="btn btn-success big">Play ' + o.cart[x].title + ' &raquo;</a>');
				}
			}
			//give the correct invoice link...
			this.$paymentSuccess.find('a.receipt-btn').attr('href', RiotAPI.rootURL + '/invoices/invoice-' + o.id);
			this.$loading.removeClass('on');
			this.$el.addClass('success-show');
		},
		handleStripeErrors: function (error) {
			var that = this,
				$errorInput;
			if (error.code === 'invalid_number' || error.code === 'incorrect_number') {
				$errorInput = $('input.card-number');
			} else if (error.code === 'invalid_cvc') {
				$errorInput = $('input.card-cvc');
			} else if (error.code === 'no_name') {
				$errorInput = $('input[name="billing_name"]');
			} else if (error.code === 'no_email') {
				$errorInput = $('input[name="user_email"]');
			} else if (error.code === 'invalid_expiry_month') {
				$errorInput = $('select.card-expiry-month');
			} else if (error.code === 'no_shipping_address') {
				$errorInput = $('input[name="shipping_address1"]');
			} else if (error.code === 'no_shipping_city') {
				$errorInput = $('input[name="shipping_city"]');
			} else if (error.code === 'no_shipping_zip') {
				$errorInput = $('input[name="shipping_zip"]');
			} else if (error.code === 'no_billing_address') {
				$errorInput = $('input[name="billing_address1"]');
			} else if (error.code === 'no_billing_city') {
				$errorInput = $('input[name="billing_city"]');
			} else if (error.code === 'no_billing_zip') {
				$errorInput = $('input[name="billing_zip"]');
			} else if (error.code === 'no_phone') {
				$errorInput = $('input[name="user_phone"]');
			}
			if ($errorInput && $errorInput.length) {
				$errorInput.addClass('invalid').focus().one('keypress.checkoutTheater', function () {
					$(this).removeClass('invalid');
					$(this).off('change.checkoutTheater');
				}).one('change.checkoutTheater', function () {
					$(this).removeClass('invalid');
					$(this).off('keypress.checkoutTheater');
				});
			}
			if (error.message) {
				if (Modernizr.touch) {
					alert(error.message);
				} else {
					alert(error.message);
				}
			} else if (typeof error === 'string') {
				alert(error);
			}
			that.$loading.removeClass('on');
		},
		processStripe: function (status, response) {
			var that = this;
			if (response.error) {
				this.handleStripeErrors(response.error);
				$('.submit-btn').prop('disabled', false).removeClass('btn-warning').addClass('btn-success').html('Checkout &raquo;');
				//remove loading gif
				that.$loading.removeClass('on');
			} else {
				var $form = this.$form,
					// token contains id, last4, and card type
					token = response.id,
					formStr;
				// Insert the token into the form so it gets submitted to the server
				$form.append($('<input type="hidden" name="stripeToken" />').val(token));
				$form.append($('<input type="hidden" name="cart" />').val(JSON.stringify(RiotAPI.getCart())));
				// and submit
				formStr = $form.serialize();
				RiotAPI.call({
					data: formStr,
					type: "post",
					after: function (msg) {
						console.log(msg);
						if (msg.success && msg.success === true) {
							that.onPaymentSuccess(msg);
						} else {
							that.handleStripeErrors(msg);
							$('.submit-btn').prop('disabled', false).removeClass('btn-warning').addClass('btn-success').html('Checkout &raquo;');
						}
					}
				});
			}
		},
		setupStripe: function () {
			var that = this,
				submitForm = function (e) {
					e.preventDefault();
					if (!that.$form.valid()) {
						//raise modal about errors
						if (!Modernizr.touch) {
							//scroll to the first input with an error
							that.$form.parents('.frame-inner').scrollTop(that.$form.parents('.frame-inner').scrollTop() + that.$form.find('input.error').offset().top - 75);
							$.showModal('Not So Fast!', 'Please check the form for errors before submitting.');
						} else {
							alert('Not so fast! Please check the form for errors before submitting.');
						}
						return false;
					}
					// Disable the submit button to prevent repeated clicks
					that.$form.find('.submit-btn').prop('disabled', true).removeClass('btn-success').addClass('btn-warning').html('Processing &hellip;');
					//show loading gif
					that.$loading.addClass('on');
					Stripe.createToken({
						number: $('input.card-number').val(),
						cvc: $('input.card-cvc').val(),
						exp_month: $('select.card-expiry-month').val(),
						exp_year: $('select.card-expiry-year').val()
					}, $.proxy(that.processStripe, that));
					// Prevent the form from submitting with the default action
					return false;
				};
			if (RiotAPI.rootURL === 'http://riotstudios.com' || RiotAPI.rootURL === 'https://riotstudios.com') {
				Stripe.setPublishableKey('pk_live_EIW1LslKLssknjDdphioytWt');
			} else {
				Stripe.setPublishableKey('pk_test_VW2GjTDVVkoJ7gNeR7JYdZed');
			}
			that.$form.submit(submitForm);
			that.$submitBtn.on('click.checkoutTheater', submitForm);
		},
		setupFormControls: function () {
			var that = this,
				cart = RiotAPI.getCart(),
				billingAddressRequired = function () {
					return !that.$form.find('input[name="same_address"]').is(':checked');
				};
			this.$form = this.$content.find('form');
			this.$cartUI = $('#checkout-shopping-cart');
			this.$shoppingCart = $('#checkout-shopping-cart');
			this.$paymentSuccess = $('#payment-success');
			this.$submitBtn = this.$el.find('#checkout-submit-btn');
			this.$cartUI.find('button[data-remove]').on('click.checkoutTheater', function () {
				var conf,
					$this = $(this);
				conf = confirm('Are you sure you want to remove this item?');
				if (conf) {
					$this.parents('li').remove();
					delete cart.items[$this.data('remove')];
					RiotAPI.updateCart(cart);
					if (RiotAPI.getObjectLength(cart.items) === 0) {
						that.hide();
					}
				}
			});
			this.$form.find('select.save').each(function () {
				var $this = $(this),
					name = $this.attr('name');
				if (cart.personal_info && cart.personal_info[name]) {
					$this.val(cart.personal_info[name]);
				}
			});
			if (cart.personal_info.hasOwnProperty('email_subscribe') && cart.personal_info.email_subscribe === false) {
				this.$form.find('input[name="email_subscribe"]').attr('checked', false);
			}
			this.$saveableInputs = this.$form.find('input.save, select.save');
			this.$saveableInputs.on('change.checkoutTheater', function () {
				var $this = $(this),
					val = $this.val(),
					name = $this.attr('name');
				if ($this.attr('type') === 'checkbox') {
					if ($this.is(':checked')) {
						val = true;
					} else {
						val = false;
					}
					if (name === 'same_address') {
						//hide or show the billing address
						if (val === true) {
							$('#checkout-form-billing').addClass('hidden');
						} else {
							$('#checkout-form-billing').removeClass('hidden');
						}
						//refresh the scrolling
						if (that.scroller) {
							that.scroller.refresh();
						}
					}
				} else if (name === 'user_name' && $('#input-billing-name').val() === '') {
					$('#input-billing-name').val(val);
				}
				cart.personal_info[name] = val;
				RiotAPI.updateCart(cart);
			});
			this.$form.validate({
				rules: {
					user_name: {
						required: true,
						minlength: 2
					},
					user_email: {
						required: true,
						email: true
					},
					shipping_address1: {
						required: true
					},
					shipping_city: {
						required: true
					},
					shipping_zip: {
						required: true,
						digits:true
					},
					billing_address1: {
						required: billingAddressRequired
					},
					billing_city: {
						required: billingAddressRequired
					},
					billing_zip: {
						required: billingAddressRequired,
						digits:true
					},
					user_phone: {
						required:function () {
							return that.$form.find('select[name="shipping_country"]').val() !== 'US';
						}
					}
				}
			});
			if (!window.Stripe) {
				$.getScript('https://js.stripe.com/v1/', $.proxy(that.setupStripe, that));
			}
		},
		show: function () {
			var that = this,
				$cartData = RiotAPI.getCart();
			if (this.shown || $cartData.total === 0) {
				return false;
			}
			this.shown = true;
			$body.trigger('checkoutTheaterShow').addClass('page-loading');
			RiotAPI.compileTemplate('checkout-form', $cartData, function (html) {
				that.$content.html(html);
				that.setupFormControls();
				that.$el.removeClass('success-show');
				$body.addClass('checkout-on');
				that.updateShipAndTotal();
				if ($.support.transition && Modernizr.csstransforms) {
					that.$el[0].offsetWidth;
					$body.addClass('checkout-shown');
				}
				if (Modernizr.touch) {
					that.scroller = new iScroll('checkout-theater', {
						useTransform: false,
						onBeforeScrollStart: function (e) {
							var target = e.target;
							while (target.nodeType !== 1) {
								target = target.parentNode;
							}
							if (target.tagName !== 'SELECT' && target.tagName !== 'INPUT' && target.tagName !== 'TEXTAREA') {
								e.preventDefault();
							}
						}
					});
				}
				that.$el.trigger('shown');
				$body.removeClass('page-loading');
				that.previousURL = window.location.href.indexOf($body.data('rooturl') + '/checkout') !== -1 ? RiotAPI.rootURL + '/store' : window.location.href;
				that.previousTitle = window.document.title === 'Checkout // Riot Studios' ? 'Store // Riot Studios' : window.document.title;
				history.pushState({page: "Riot Studios | Checkout"}, 'Checkout // Riot Studios', RiotAPI.rootURL + '/checkout');
			});
		},
		hide: function (e) {
			var that = this;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (!this.shown) {
				return false;
			}
			this.shown = false;
			if ($.support.transition && Modernizr.csstransforms) {
				$body.removeClass('checkout-shown');
				this.$el.one($.support.transition.end, function () {
					that.$content.html('');
					$body.removeClass('checkout-on');
					that.$el.trigger('hidden');
				});
			} else {
				that.$content.html('');
				$body.removeClass('checkout-on checkout-shown');
				this.$el.trigger('hidden');
			}
			history.pushState({page: "Riot Studios | Store"}, this.previousTitle, this.previousURL);
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new CheckoutTheater(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		var $checkoutTheater = $('#checkout-theater');
		$body = $('body');
		if ($checkoutTheater.length) {
			$checkoutTheater.checkoutTheater();
		} else {
			$body.append($('script#checkout-theater-html').html());
			$('#checkout-theater').checkoutTheater();
		}
	});
});