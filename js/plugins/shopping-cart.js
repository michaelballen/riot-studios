//Shopping Cart 0.0
/*global RiotAPI*/
define(['jquery', 'plugins/riot-plugins', 'plugins/product-slider', 'plugins/riot-api', 'plugins/checkout-theater'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'shoppingCart',
		$body,
		$html,
		//===Class
		ShoppingCart = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({},this.$el.data(),options);
			this.init();
		};
	ShoppingCart.prototype = {
		init: function () {
			var that = this;
			this.active = true;
			this.$main = $('#store-main-display');
			this.$main.find('a[data-buy_now]').on('click.shoppingCart', $.proxy(that.showAddForm, that));
			this.$add_form = this.$el.find('.add-to-cart');
			this.$cancel_btn = this.$add_form.find('a.btn-danger').on('click.shoppingCart', $.proxy(that.hideAddForm, that));
			this.$keep_shopping_btn = this.$add_form.find('a.btn-warning').on('click.shoppingCart', $.proxy(that.hideAddForm, that));
			this.$add_to_cart_btn = this.$add_form.find('a[data-add_to_cart]');
			this.$view_cart_btn = this.$add_form.find('a[data-checkout_theater]');
			this.$topCartPreview = $('#store-top .cart-preview .preview-text');
			this.$topCartPreviewButton = $('#store-top .cart-preview .btn');
			this.productSlider = $('#product-slider');
			this.productSlider.on('productLoading', $.proxy(that.hideAddForm, that));
			this.$bottomUI = this.$el.find('.bottom-updater');
			this.$bottomUITotal = this.$bottomUI.find('.fltlt');
			$('#checkout-theater').on('shown', $.proxy(this.hideAddForm, this));
			$body.off('pageLoaded.store.cart pageLoading.store.cart checkoutTheaterShow updateShoppingCart.shoppingCart');
			$body.on('pageLoaded.store.cart', function (e, m) {
				if (m === 'store' && !that.active) {
					$('#shopping-cart').shoppingCart();
				}
			}).on('pageLoading.store.cart', function (e, url) {
				if (url !== RiotAPI.rootURL + '/store' && url !== RiotAPI.rootURL + '/store/') {
					that.hideAddForm();
					that.hideBottomUI();
					that.active = false;
				}
			}).on('checkoutTheaterShow', $.proxy(this.hideAddForm, this)).on('updateShoppingCart.shoppingCart', function () {
				that.updateAddForm();
				that.bottomUI();
			});
			if (window.RiotAPI !== undefined) {
				$body.trigger('updateShoppingCart');
			} else {
				$(window).on('apiReady.shoppingCart', function () {
					$body.trigger('updateShoppingCart');
				});
			}
		},
		bottomUI: function () {
			var total = RiotAPI.getCart().total;
			if (total > 0) {
				//update bottom ui and show
				this.showBottomUI(total);
			} else {
				//hide bottom UI
				this.hideBottomUI();
			}
		},
		showBottomUI: function (total) {
			var numItems = 0,
				cart = RiotAPI.getCart(),
				x,
				previewStr = '';
			if (!total) {
				total = cart.total;
			}
			total = Math.round(total * 100) / 100;
			for (x in cart.items) {
				numItems += 1;
			}
			if (numItems === 1) {
				previewStr = '1 Item';
			} else {
				previewStr = numItems + ' Item';
			}
			//update the top right ui first...
			this.$topCartPreview.html(previewStr + ' | $' + total);
			this.$topCartPreviewButton.removeClass('hidden');
			this.$bottomUITotal.text('$' + total);
			this.$bottomUI.addClass('on');
			this.$bottomUI[0].offsetWidth;
			this.$bottomUI.addClass('show');
		},
		hideBottomUI: function () {
			this.$topCartPreview.html('No items in cart');
			this.$topCartPreviewButton.addClass('hidden');
			if ($.support.transition) {
				this.$bottomUI.removeClass('show');
				this.$bottomUI.one($.support.transition.end, function () {
					$(this).removeClass('on');
				});
			} else {
				this.$bottomUI.removeClass('on show');
			}
		},
		showAddForm: function (e) {
			var that = this,
				$add = that.$add_form,
				meta = that.$main.data('meta'),
				cart = RiotAPI.getCart();
			if (typeof e === 'object') {
				e.preventDefault();
			}
			//first put in all the info for the currently shown item...
			that.updateAddForm(meta);
			//unhide it
			that.$add_to_cart_btn.on('click.shoppingCart', $.proxy(that.addToCart, that));
			$body.addClass('addtocart-show');
			$add[0].offsetWidth;//reflow
			$body.addClass('addtocart-on');
			//focus the price button if it's nyop and not yet in the cart
			if (meta.nyop === "1" && !cart.items.hasOwnProperty(meta.id) && !Modernizr.touch) {
				$add.find('input[name="price"]').focus().select();
			}
		},
		hideAddForm: function (e) {
			var that = this,
				$add = that.$add_form;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			if (!$body.hasClass('addtocart-show')) {
				return false;
			}
			that.$add_to_cart_btn.off('click.shoppingCart');
			if ($.support.transition) {
				$body.removeClass('addtocart-on');
				$add.one($.support.transition.end, function () {
					$body.removeClass('addtocart-show');
				});
			} else {
				$add.removeClass('addtocart-on addtocart-show');
			}
		},
		updateAddForm: function (meta) {
			var	that = this,
				$add,
				cart = RiotAPI.getCart();
			if (meta !== undefined && meta.hasOwnProperty('id') && that.add_id && that.add_id === meta.id) {
				return false;
			} else if (meta === undefined) {
				meta = that.$main.data('meta');
			}
			$add = that.$add_form;
			if (meta.nyop === "1") {
				$add.find('.add-title').text("Name Your Price!").addClass('bold');
			} else {
				$add.find('.add-title').text("Add to Cart").removeClass('bold');
			}
			$add.find('.add-to-cart-inner h2').html('<img src="' + meta.thumb + '" /> ' + meta.post_title);
			$add.find('input[name="qty"], input[name="price"]').off('change.shoppingCart');
			//set price, qty, and button text according to cart if it's in cart
			if (cart.items.hasOwnProperty(meta.id)) {
				//if it is in the cart...
				$add.find('input[name="qty"]').val(cart.items[meta.id].qty).on('change.shoppingCart', function () {
					that.addToCart();
				});
				$add.find('input[name="price"]').val(cart.items[meta.id].price).on('change.shoppingCart', function () {
					that.addToCart();
				});
				//hide cancel/add buttons
				this.$add_to_cart_btn.addClass('hidden');
				this.$cancel_btn.addClass('hidden');
				//show keep/cart
				this.$view_cart_btn.removeClass('hidden');
				this.$keep_shopping_btn.removeClass('hidden');
			} else {
				//not in the cart
				$add.find('input[name="qty"]').val('1').off('change.shoppingCart');
				$add.find('input[name="price"]').val(parseFloat(meta.price).toFixed(2));
				//show cancel/add buttons
				this.$add_to_cart_btn.removeClass('hidden');
				this.$cancel_btn.removeClass('hidden');
				//hide keep/cart
				this.$view_cart_btn.addClass('hidden');
				this.$keep_shopping_btn.addClass('hidden');
			}
			that.add_id = meta.id;
		},
		addToCart: function (e) {
			var that = this,
				id = that.add_id,
				meta = that.$main.data('meta'),
				o = {},
				cart = RiotAPI.getCart();
			if (!id) {
				return false;
			}
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			o.id = id;
			o.type = meta.product_type;
			o.title = meta.post_title;
			o.thumb = meta.thumb;
			o.price = parseFloat(that.$add_form.find('input[name="price"]').val());
			o.qty = parseFloat(that.$add_form.find('input[name="qty"]').val());
			if (meta.price > o.price && o.qty > 1) {
				o.qty = 1;
				alert("Sorry, but we can only offer 1 of these items below the recommended price of $" + meta.price);
			}
			o.shipping = parseFloat(meta.shipping);
			cart.items[id.toString()] = o;
			RiotAPI.updateCart(cart);
			//now switch the buttons
			this.$add_to_cart_btn.addClass('hidden');
			this.$view_cart_btn.removeClass('hidden');
		},
		getCartTotal: function () {
			var cart = RiotAPI.getCart(),
				items = cart.items,
				x,
				current,
				total = 0;
			for (x in items) {
				current = items[x];
				total += (current.price * current.qty);
			}
			return total;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data || (data && typeof option !== 'string')) {
				$this.data(pluginName, null);
				$this.data(pluginName, (data = new ShoppingCart(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===Data-API
	$(function () {
		var $shoppingCart;
		$body = $('body');
		$html = $('html');
		$shoppingCart = $('#shopping-cart');
		if ($shoppingCart.length) {
			$('#shopping-cart').shoppingCart();
		} else {
			$body.append($('script#shopping-cart-html').html());
			$('#shopping-cart').shoppingCart();
		}
	});
}(this.jQuery));