//Shopping Cart 0.0
define(['jquery', 'plugins/product-slider', 'plugins/riot-api', 'plugins/checkout'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'shoppingCart',
		$body,
		//===Class
		ShoppingCart = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({},this.$el.data(),options);
			this.init();
		};
	ShoppingCart.prototype = {
		init: function () {
			var that = this;
			this.cart = this._getCart();
			this.$main = $('#store-main-display');
			this.$main.find('a[data-buy_now]').on('click.shoppingCart', $.proxy(that.showAddForm, that));
			this.$add_form = this.$el.find('.add-to-cart');
			this.$add_close_btn = this.$add_form.find('a[data-close]').on('click.shoppingCart', $.proxy(that.hideAddForm, that));
			this.$add_to_cart_btn = this.$add_form.find('a[data-add_to_cart]');
			this.$view_cart_btn = this.$add_form.find('a[data-view_cart]');
			this.productSlider = $('#product-slider');
			this.productSlider.on('productLoading', $.proxy(that.hideAddForm, that));
			$body.off('pageLoaded.store.cart');
			$body.on('pageLoaded.store.cart', function (e, m) {
				if (m === 'store') {
					$('#shopping-cart').shoppingCart();
				}
			}).on('pageLoading.store.cart', $.proxy(that.hideAddForm, that));
		},
		showAddForm: function (e) {
			var that = this,
				$add = that.$add_form,
				meta = that.$main.data('meta');
			if (typeof e === 'object') {
				e.preventDefault();
			}
			
			//first put in all the info for the currently shown item...
			that.updateAddForm(meta);
			
			//unhide it
			that.$add_to_cart_btn.on('click.shoppingCart', $.proxy(that.addToCart, that));
			$add.addClass('show');
			$add[0].offsetWidth;//reflow
			$add.addClass('on');
		},
		hideAddForm: function (e) {
			var that = this,
				$add = that.$add_form;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (!$add.hasClass('show')) {
				return false;
			}
			that.$add_to_cart_btn.off('click.shoppingCart');
			$add.removeClass('on');
			//animate it up
			setTimeout(function () {
				$add.removeClass('show');
			}, 300);
		},
		updateAddForm: function (meta) {
			var	that = this,
				$add;
			if (that.add_id && that.add_id === meta.id) {
				return false;
			}
			this.cart = this._getCart();
			$add = that.$add_form;
			if (meta.nyop === "1") {
				$add.find('.add-title').text("Name Your Price!").addClass('bold');
			} else {
				$add.find('.add-title').text("Add to Cart").removeClass('bold');
			}
			$add.find('h2').html('<img src="' + meta.thumb + '" /> ' + meta.post_title);
			
			//set price, qty, and button text according to cart if it's in cart
			if (this.cart.items.hasOwnProperty(meta.id)) {
				$add.find('input[name="qty"]').val(this.cart.items[meta.id].qty);
				$add.find('input[name="price"]').val(this.cart.items[meta.id].price);
				this.$add_to_cart_btn.addClass('hidden');
				this.$view_cart_btn.removeClass('hidden');
			} else {
				$add.find('input[name="qty"]').val('1');
				$add.find('input[name="price"]').val(meta.price.toCurrency());
				this.$add_to_cart_btn.removeClass('hidden');
				this.$view_cart_btn.addClass('hidden');
			}
			that.add_id = meta.id;
		},
		addToCart: function () {
			var that = this,
				id = that.add_id,
				meta = that.$main.data('meta'),
				o = {};
			if (!id) {
				return false;
			}
			
			o.id = id;
			o.title = meta.post_title;
			o.thumb = meta.thumb;
			o.price = parseFloat(that.$add_form.find('input[name="price"]').val());
			o.qty = parseFloat(that.$add_form.find('input[name="qty"]').val());
			this.cart.items[id.toString()] = o;
			this._updateCart();
		},
		_setupCart: function () {
			var cart = {};
			cart.personal_info = {};
			cart.items = {};
			localStorage.setItem("cart", JSON.stringify(cart));
			return cart;
		},
		_updateCart: function () {
			localStorage.setItem("cart", JSON.stringify(this.cart));
		},
		_getCart: function () {
			var cart = localStorage.getItem("cart");
			if (!cart) {
				return this._setupCart();
			}
			return $.parseJSON(cart);
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
			$this.data(pluginName, (data = new ShoppingCart(this, options)));
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		$body = $('body');
		$('#shopping-cart').shoppingCart();
	});
}(this.jQuery));