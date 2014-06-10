//Riot API 0.0
/*global Handlebars*/
define(['jquery', 'vendor/handlebars'], function ($) {
	"use strict";
	//definitions
	var RiotAPI = function () {
			this.cart = this.setupCart;
			this.url = $('body').data('rooturl') + '/api';
			this.templates = {};
			this.defaultCall = {
				url: this.url,
				data: {},
				before: false,
				dataType: 'json'
			};
			this.init();
		};
	RiotAPI.prototype = {
		init: function () {
			this.rootURL = $('body').data('rooturl');
		},
		getPage: function (page, after) {
			var url = this.rootURL + '/' + page;
			//do an ajax call for that url
			$.ajax({
				url:url,
				data: {
					ajax:1
				}
			}).done(function (msg) {
				if (typeof after === 'function') {
					after(msg);
				}
			});
		},
		compileTemplate: function (temp_name, data, after) {
			var url,
				result,
				that = this;
			//if we already have the compiler saved for that template...
			if (this.templates[temp_name]) {
				result = this.templates[temp_name](data);
				if (typeof after === 'function') {
					after(result);
					return true;
				} else {
					return result;
				}
			}
			//fetch the compile html from the templates folder using ajax
			url = $('body').data('rooturl') + '/template?temp_name=' + temp_name;
			$.ajax({
				url:url,
				data: {
					ajax:1
				}
			}).done(function (msg) {
				if (typeof after === 'function') {
					//save the template compiler
					that.templates[temp_name] = Handlebars.compile(msg);
					result = that.templates[temp_name](data);
					if (typeof after === 'function') {
						after(result);
					}
				}
			});
		},
		call: function (o) {
			/*
			normal params:
			------------------------------
			data: {
				action: (i.e. sendEmail)
			}
			before - function to run before the call
			after - function to run after
			*/
			o = $.extend({}, this.defaultCall, o);
			if (o.before && typeof o.before === 'function') {
				o.before();
			}
			$.ajax(o).done(function (data, status) {
				if (typeof o.after === 'function' && status === "success") {
					if (data.success) {
						o.after(data.data, status);
					} else if (data.errormsg) {
						o.after(data.errormsg, 'api-error');
						console.log('riot api error -> ' + data.errormsg);
					}
				}
			});
		},
		getCart: function () {
			var cart = localStorage.getItem("cart");
			if (!cart) {
				return this.setupCart();
			}
			return $.parseJSON(cart);
		},
		updateCart: function (c) {
			var ship = 0,//base shipping
				total = 0,
				i;
			if (!c) {
				c = this.cart;
			}
			//calc item total and shipping
			for (i in c.items) {
				if (c.items[i].hasOwnProperty('shipping')) {
					ship += (c.items[i].shipping * c.items[i].qty);
					//if the shipping country is set and not us
					if (c.personal_info.hasOwnProperty('shipping_country') && c.personal_info.shipping_country !== "US") {
						ship += c.items[i].qty;//$1 extra per qty
					}
				}
				total += (c.items[i].qty * c.items[i].price);
			}
			if (ship > 0) {
				ship = Math.round( (parseFloat(ship) + this.getBaseShipping()) * 100 ) / 100;
				total = parseFloat(ship + total);
			}
			c.total = total;
			c.shipping = ship;
			localStorage.setItem("cart", JSON.stringify(c));
			$('body').trigger('updateShoppingCart');
			this.cart = this.getCart();
		},
		clearCartItems: function () {
			var cart = this.getCart();
			cart.items = {};
			cart.shipping = 0;
			cart.total = 0;
			this.updateCart(cart);
		},
		getBaseShipping: function () {
			var $shoppingCart;
			if (this.baseShipping) {
				return parseFloat(this.baseShipping);
			}
			$shoppingCart = $('#shopping-cart');
			this.baseShipping = parseFloat(($shoppingCart.length > 0 && $shoppingCart.data('base_shipping')) ? $shoppingCart.data('base_shipping') : 1.99);
			return this.baseShipping;
		},
		setupCart: function () {
			var cart = {};
			this.getBaseShipping();
			cart.personal_info = {};
			cart.items = {};
			localStorage.setItem("cart", JSON.stringify(cart));
			return cart;
		},
		getObjectLength: function (o) {
			var x,
				length = 0;
			for(x in o){
				if (o.hasOwnProperty(x)) {
					length += 1;
				}
			}
			return length;
		},
		validateEmail: function (email) {
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			if (reg.test(email) === false) {
				return false;
			}
			return true;
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
	};
	$(function () {
		//===expose global
		window.RiotAPI = new RiotAPI();
		$(window).trigger('apiReady');
		$('html').addClass('apiReady');
	});
}(this.jQuery));