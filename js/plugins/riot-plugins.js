/*global Modernizr*/
//first some javscript customization
String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
};

String.prototype.toCurrency = function() {
	var that = this;
    that = (isNaN(that) || that === '' || that === null) ? 0.00 : that;
	that = parseFloat(that);
    return that.toCurrency();
};

Number.prototype.toCurrency = function() {
	var that = this;
    that = isNaN(that) || that === '' || that === null ? 0.00 : that;
    return that.toFixed(2);
};
require(['jquery'], function ($) {
	"use strict";
	$('body').on('click.toggleSection', '[data-toggle_section]', function (e) {
		var $this = $(this),
			$target = $($this.attr('href'));
		if ($target.length) {
			$target.toggleClass('on');
			$this.toggleClass('on');
			if (Modernizr.touch) {
				$('body').trigger('refreshScroll');
			}
		}
		e.preventDefault();
	});
	//general jQuery plugins
	$.fn.rotate = function (deg) {
		return this.each(function () {
			var $this = $(this);
			$this[0].style.webkitTransform = 'rotate(' + deg.toString() + 'deg)';
		});
	};
	$.fn.hasAttr = function (a) {
		return $(this).attr(a) !== undefined;
	};
	$.fn.afterTransition = function (onComplete) {
		if ($.support.transition) {
			$(this).one($.support.transition.end, onComplete);
		} else {
			onComplete();
		}
	};
	$.fn.smartResize = function (o) {
		var $this = $(this),
			timer;
		//setup defaults
		o = $.extend({
			time: 300,
			callback: false,
			namespace: false
		}, o);
		if (typeof o.callback !== 'function') {
			return false;
		}
		if (typeof o.namespace === 'string') {
			o.namespace = 'resize.' + o.namespace;
		} else {
			o.namespace = 'resize';
		}
		$this.on(o.namespace, function (e) {
			if (timer) {
				clearTimeout(timer);
			}
			timer = setTimeout(function () {
				o.callback(e);
				timer = null;
			}, o.time);
		});
	};
	return $;
});