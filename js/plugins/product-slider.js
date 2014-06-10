//Product Slider 0.0
/*global RiotAPI, Modernizr, iScroll*/
define(['jquery', 'plugins/scroller', 'plugins/riot-plugins', 'plugins/ajax-loader', 'vendor/bootstrap/bootstrap-transition', 'plugins/riot-api'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'productSlider',
		$body,
		$window,
		//===Class
		ProductSlider = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, this.$el.data(), options);
			this.init();
		};
	ProductSlider.prototype = {
		init: function () {
			var that = this;
			this.rooturl = $body.data('rooturl');
			this.$ul = this.$el.find('ul');
			this.$li = this.$ul.find('li');
			this.$loading = $('.content-outer.store .loading');
			this.$main = $('#store-main-display');
			this.$buy_now = $('[data-buy_now]');
			this.$toggle = this.$el.find('a[data-toggle_product_slider]');
			this.$topHeaderTitle = $('#store-top h3 .title');
			this.$websiteButton = $('#product-website-link');
			this.$trailerButton = $('#product-trailer-link');
			this.shown = true;
			this.$productCon = this.$el.find('.product-con');
			if (Modernizr.touch) {
				this.setSizing();
				this.setupScrolling();
				this.$toggle.on('click.productSlider', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.productSlider', $.proxy(that.toggle, that));
			} else {
				this.$scrollLeftButton = $('#product-slider-arrow-left').on('click.productSlider', $.proxy(this.scrollLeft, this));
				this.$scrollRightButton = $('#product-slider-arrow-right').on('click.productSlider', $.proxy(this.scrollRight, this));
				this.$toggle.on('click.productSlider', $.proxy(that.toggle, that));
				this.setSizing();
			}
			this._setupResize();
			this._setupArticleClick();
			this.setupCurrent();
			$body.off('pageLoaded.store.slider');
			$body.on('pageLoaded.store.slider', function (e, m) {
				if (m === 'store') {
					$('#product-slider').productSlider();
				}
			}).on('updateShoppingCart.productSlider', function () {
				that.adjustBuyButton();
			});
		},
		toggle: function (e) {
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.stopPropagation();
				e.preventDefault();
			}
			this.$el.toggleClass('slider-off');
		},
		hide: function () {
			this.$el.addClass('slider-off');
			$body.trigger('refreshScroll');
			this.shown = false;
		},
		show: function () {
			this.$el.removeClass('slider-off');
			$body.trigger('refreshScroll');
			this.shown = true;
		},
		activateProduct: function (id) {
			var that = this,
				$slider_item;
			if (this.transitioning || this.currentID === id) {
				return false;
			}
			//transitioning means we won't be allowed to activate a product til we're done
			this.transitioning = true;
			this.$el.trigger('productLoading');
			//show loading div
			this.$loading.addClass('on');
			//transition out the old product
			if ($.support.transition) {
				that.mainTop = that.$main.position().top;
				that.$main.addClass('changing-content');
				that.$main[0].offsetWidth;
				that.$main.one($.support.transition.end, function () {
					that.$main.addClass('hidden').removeClass('off');
				});
				that.$main.addClass('moving off');
			} else {
				that.$main.addClass('hidden');
			}
			//transition the actual slider at the top
			$slider_item = $('#product-' + id);
			this.$el.find('li.active').removeClass('active');
			$slider_item.addClass('active');
			//start the api call
			RiotAPI.call({
				data: {
					action: 'getProduct',
					id: id
				},
				after: function (msg) {
					that.fillContentAndShow(msg);
				}
			});
		},
		scrollRight: function (e) {
			var goTo = (this.leftPosition !== undefined) ? this.leftPosition : 0,
				fullWidth;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			fullWidth = this.$el.outerWidth();
			goTo = goTo - fullWidth;
			goTo = Math.max(goTo, this.maxScroll);
			if (goTo === this.leftPosition) {
				return false;
			}
			this._moveUL(goTo);
		},
		scrollLeft: function (e) {
			var goTo = (this.leftPosition !== undefined) ? this.leftPosition : 0,
				fullWidth;
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			fullWidth = this.$el.outerWidth();
			goTo = goTo + fullWidth;
			goTo = Math.min(goTo, 0);
			if (goTo === this.leftPosition) {
				return false;
			}
			this._moveUL(goTo);
		},
		_moveUL: function (x) {
			if (Modernizr.csstransforms) {
				this.$ul[0].style[Modernizr.prefixed('transform')] = 'translate(' + x + 'px, 0)';
			} else {
				this.$ul[0].style.left = x + 'px';
			}
			if (x === 0) {
				this.$scrollLeftButton.addClass('off');
			} else {
				this.$scrollLeftButton.removeClass('off');
			}
			if (x === this.maxScroll) {
				this.$scrollRightButton.addClass('off');
			} else {
				this.$scrollRightButton.removeClass('off');
			}
			this.LeftPosition = x;
			return x;
		},
		setSizing: function () {
			var itemWidth = this.$li.outerWidth(),
				totalWidth = (itemWidth * this.$li.length) + (parseInt($(this.$li[1]).css('marginLeft'), 10) * (this.$li.length - 1));
			this.$ul.width(totalWidth).removeClass('unsized');
			this.maxScroll = (this.$el.outerWidth() - this.$ul.outerWidth()) - parseInt(this.$el.css('paddingRight'), 10);
			if (!Modernizr.touch) {
				this._moveUL(0);
				if (totalWidth > this.$el.width()) {
					this.$scrollRightButton.removeClass('off');
				} else {
					this.$scrollLeftButton.addClass('off');
					this.$scrollRightButton.addClass('off');
				}
			}
		},
		_setupResize: function () {
			if (typeof $.fn.smartResize === 'function') {
				$window.smartResize({
					callback: $.proxy(this.setSizing, this),
					namespace: 'productSlider'
				});
				return true;
			}
			setTimeout($.proxy(this._setupResize, this), 300);
		},
		_setupArticleClick: function () {
			var that = this;
			that.$li.on('click.productSlider', function (e) {
				var $this = $(this),
					id = $this.data('id');
				e.preventDefault();
				that.activateProduct(id);
			});
		},
		setupCurrent: function () {
			var that = this,
				meta = this.$main.data('meta');
			this.currentID = meta.id;
			if ($('html').hasClass('apiReady')) {
				this.adjustBuyButton();
				this.$buy_now.removeClass('hidden');
				return true;
			}
			$(window).on('apiReady.productSlider', function () {
				that.adjustBuyButton();
				that.$buy_now.removeClass('hidden');
			});
		},
		adjustBuyButton: function (id) {
			var cart = RiotAPI.getCart();
			id = parseInt(id, 10) || parseInt(this.currentID, 10);
			if (cart.items.hasOwnProperty(id)) {
				//this item is in the cart, show edit button
				this.$buy_now.html('Edit Cart &raquo;');
			} else {
				this.$buy_now.html('Buy Now &raquo;');
			}
		},
		fillContentAndShow: function (data) {
			var that = this,
				price_text = data.nyop === "1" ? "Name Your Price!" : "$" + data.price.toCurrency(),
				changeTitle = function () {
					var url;
					that.$topHeaderTitle.text(data.post_title);
					if (Modernizr.history) {
						url = that.rooturl + "/store/" + data.post_name;
						document.title = "Riot Store // " + data.post_title;
						history.pushState({page: "Riot Store // " + data.post_title}, data.post_title, url);
					}
				};
			this.$main.data('meta', data);
			this.$main.find('h2').text(data.post_title);
			this.$main.find('.side-con img').attr('src', data.image);
			this.$main.find('.text-con').html(data.post_content);
			this.$main.find('.price-con p').html(price_text);
			if (data.trailer_link) {
				this.$trailerButton.attr({
					'data-play_video': data.trailer_id,
					href: data.trailer_link
				}).removeClass('hidden');
			} else {
				this.$trailerButton.addClass('hidden');
			}
			if (data.website_link) {
				this.$websiteButton.attr({
					href: data.website_link
				}).removeClass('hidden');
			} else {
				this.$websiteButton.addClass('hidden');
			}
			this.adjustBuyButton(data.id);
			if ($.support.transition) {
				this.$main.off($.support.transition.end);
				this.$main.addClass('hidden').removeClass('moving off');
				this.$main[0].offsetWidth;//reflow
				this.$main.removeClass('hidden').addClass('moving before');
				this.$main[0].offsetWidth;//reflow
				this.$main.removeClass('before');
				this.$main.one($.support.transition.end, function () {
					that.$main.removeClass('moving changing-content');
					that.$loading.removeClass('on');
					changeTitle();
					that.transitioning = false;
					$('body').trigger('refreshScroll');
				});
			} else {
				this.$main.removeClass('hidden');
				this.$loading.removeClass('on');
				changeTitle();
				this.transitioning = false;
				$('body').trigger('refreshScroll');
			}
			this.currentID = parseInt(data.id, 10);
		},
		setupScrolling: function () {
			if (typeof iScroll !== 'function') {
				$body.one('scrollerReady', $.proxy(this.setupScrolling, this));
				return false;
			}
			this.scroller = new iScroll(this.$ul.parent()[0], {
				hScroll:true,
				vScroll:false
			});
			return true;
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new ProductSlider(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	//===start script
	$(function () {
		$body = $('body');
		$window = $(window);
		$('#product-slider').productSlider();
	});
}(this.jQuery));