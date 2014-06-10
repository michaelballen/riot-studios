//Socializer 0.0
/*global twttr, FB, RiotAPI, Modernizr, iScroll*/
require(['jquery', 'plugins/scroller', 'plugins/riot-api', 'plugins/riot-plugins', 'plugins/riot-modal'], function ($) {
	"use strict";
	//definitions
	var pluginName = 'socializer',
		$body,
		$window = $(window),
		//===Class
		Socializer = function (element, options) {
			this.$el = $(element);
			this.options = $.extend({}, $.fn[pluginName].defaults, this.$el.data(), options);
			this.init();
		};
	Socializer.prototype = {
		init: function () {
			var that = this;
			this.state = this.getState();
			this.$feedTick = this.$el.find('.top-dial .tick-line');
			this.$topContainer = this.$el.find('.top-container');
			this.$bottomContainer = this.$el.find('.bottom-container');
			this.$userPostTextarea = this.$bottomContainer.find('#social_user_post');
			this.$modal = $('#socializer-modal');
			this.currentTweetPage = 1;
			this.maxTweetPages = $('#twitter-feed').data('max_pages');
			this._setupModal();
			$('#socializer_fb_like').on('click.socializer', $.proxy(this.fbLike, this));
			if (Modernizr.touch) {
				$body.off('click.socializer')
					.on('click.socializer', '[data-toggle_desktop_social]', function (e) {
						e.preventDefault();
						return false;
					})
					.on('touchend.socializer', '[data-toggle_desktop_social]', $.proxy(this.toggle, this));
				this.$userSubmitBtn = this.$bottomContainer.find('#social_user_submit').on('click.socializer', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.socializer', function () {
					var cls = that.$bottomContainer.data('type');
					switch (cls) {
						case 'twitter':
							that.tweetWindow();
							break;
						case 'facebook':
							that.fbPost();
							break;
						case 'email':
							that.emailPost();
							break;
					}
				});
				this.$feedButtons = this.$el.find('.feed-button')
					.on('click.socializer', function (e) {
						e.preventDefault();
						return false;
					}).on('touchend.socializer', function () {
						var cls = $(this).data('feed_class');
						if (!that.$topContainer.hasClass(cls)) {
							that.$topContainer.removeClass('news friends').addClass(cls);
						}
						switch (cls) {
							case 'friends':
								that.friendScroll.refresh();
								break;
							case 'news':
								that.tweetScroll.refresh();
								break;
						}
					});
				this.$messageButtons = this.$el.find('.input-selector').on('click.socializer', function (e) {
					e.preventDefault();
					return false;
				}).on('touchend.socializer', function () {
					var cls = $(this).data('tick_class');
					if (!that.$bottomContainer.hasClass(cls)) {
						that.$bottomContainer.removeClass('facebook twitter email').addClass(cls).data('type', cls);
						if (cls === 'email') {
							that.$userPostTextarea.attr('placeholder', 'Your Message...');
							that.$userPostSubmitText.text('Send');
							that.$userEmailInput.focus();
						} else if (cls === 'twitter') {
							that.$userPostSubmitText.text('Tweet');
							that.$userPostTextarea.attr('placeholder', 'Your Tweet...').focus();
						} else {
							that.focusFacebook();
						}
					}
				});
			} else {
				$body
					.off('click.socializer')
					.on('click.socializer.toggle', '[data-toggle_desktop_social]', $.proxy(this.toggle, this))
					.on('click.socializer.focus', '[data-socializer_focus]', function (e) {
						var focusVal = $(this).data('socializer_focus');
						switch (focusVal) {
							case 'facebook':
								that.focusFacebook();
								break;
						}
						e.preventDefault();
					});
				this.$loadTweetsButton = this.$el.find('[data-load_tweets]').on('click.socializer.twitterLoad', $.proxy(this.loadTweets, this));
				this.$userSubmitBtn = this.$bottomContainer.find('#social_user_submit').on('click.socializer', function (e) {
					var cls = that.$bottomContainer.data('type');
					switch (cls) {
						case 'twitter':
							that.tweetWindow();
							break;
						case 'facebook':
							that.fbPost();
							break;
						case 'email':
							that.emailPost();
							break;
					}
					e.preventDefault();
				});
				this.$feedButtons = this.$el.find('.feed-button').on('click.socializer', function (e) {
					var cls = $(this).data('feed_class');
					e.preventDefault();
					if (!that.$topContainer.hasClass(cls)) {
						that.$topContainer.removeClass('news friends').addClass(cls);
						that.$el.find('.feed-content').scrollTop(0);
					}
				});
				this.$messageButtons = this.$el.find('.input-selector').on('click.socializer', function (e) {
					var cls = $(this).data('tick_class');
					e.preventDefault();
					if (!that.$bottomContainer.hasClass(cls)) {
						that.$bottomContainer.removeClass('facebook twitter email').addClass(cls).data('type', cls);
						if (cls === 'email') {
							that.$userPostTextarea.attr('placeholder', 'Your Message...');
							that.$userPostSubmitText.text('Send');
							that.$userEmailInput.focus();
						} else if (cls === 'twitter') {
							that.$userPostSubmitText.text('Tweet');
							that.$userPostTextarea.attr('placeholder', 'Your Tweet...').focus();
						} else {
							that.$userPostSubmitText.text('Post');
							that.$userPostTextarea.attr('placeholder', 'Your Post...').focus();
						}
					}
				});
			}
			this.$userPostSubmitText = this.$userSubmitBtn.find('.btn-text');
			this.$messageTick = this.$el.find('.bottom-dial .measure-line');
			this.$userEmailInput = this.$el.find('#social_user_email');
			if (typeof twttr === 'object') {
				twttr.ready(function (twttr) {
					twttr.events.bind('tweet', function () {
						that.$userPostTextarea.val('');
					});
				});
			}
			$window.smartResize({
				namespace: 'socializer',
				callback: $.proxy(this.handleResize, this)
			});
			if (Modernizr.touch) {
				//setup touch scrolling...
				this.tweetScroll = new iScroll(this.$el.find('.feed-content')[0]);
				this.friendScroll = new iScroll(this.$el.find('#socializer-friends')[0]);
			}
		},
		loadTweets: function (e) {
			var that = this;
			if (typeof e === 'object') {
				e.preventDefault();
			}
			if (this.loadingTweets) {
				return false;
			}
			this.loadingTweets = true;
			//give the button a loading appearance.
			that.$loadTweetsButton.addClass('show-loading').blur();
			RiotAPI.call({
				data: {
					action: 'loadMoreTweets',
					tweet_page: (this.currentTweetPage + 1)
				},
				after: function (msg) {
					that.$loadTweetsButton.removeClass('show-loading').before(msg);
					that.currentTweetPage += 1;
					if (that.currentTweetPage >= that.maxTweetPages) {
						that.$loadTweetsButton.remove();
					}
					that.loadingTweets = false;
				}
			});
		},
		focusFacebook: function () {
			if (this.state === 'small') {
				this.toggle();
			}
			this.$bottomContainer.removeClass('twitter email').addClass('facebook').data('type', 'facebook');
			this.$userPostSubmitText.text('Post');
			this.$userPostTextarea.attr('placeholder', 'Your Post...').focus();
			this.$userPostTextarea.focus();
		},
		tweetWindow: function () {
			var txt = this.$userPostTextarea.val();
			window.open("https://twitter.com/intent/tweet?text=" + txt + '&via=riotstudios',
				"Twitter",
				"status = 1, left = 450, top = 270, height = 300, width = 400, resizable = 0");
		},
		fbPost: function () {
			var that = this,
				txt = this.$userPostTextarea.val();
			if (!txt) {
				that.modalMessage('Not So Fast!', 'Make sure you write something in the message area before submitting.');
				return false;
			}
			FB.login(function(response) {
				if (response.authResponse) {
					that.fbAuth = response.authResponse;
					that.fbUserID = response.userID;
					$.ajax({
						data: {
							message: txt,
							access_token: that.fbAuth.accessToken,
							picture: $('meta[property="og:image"]').attr('value'),
							link: $('meta[property="og:url"]').attr('value'),
							name: $('meta[property="og:title"]').attr('value'),
							description: $('meta[property="og:description"]').attr('value'),
							type: 'link',
							application: {
								name: 'Riot Studios',
								id: '201900699822506'
							}
						},
						url:"https://graph.facebook.com/me/feed",
						type:'POST'
					}).done(function (msg) {
						if (msg.id) {
							that.modalMessage('Boom, Posted!', 'Your message was just posted all up on Facebook.');
							that.$userPostTextarea.val('');
						} else {
							that.modalMessage('What the ...?', 'There was an error posting to Facebook. Bummer. Go ahead and try again later.');
						}
					});
				} else {
					// cancelled
					that.modalMessage('Ya Gotta Login', 'If you don\'t log in to Facebook, we can\'t post your message. Give it a try; we promise it won\'t hurt.');
				}
			}, {
				scope: 'email,publish_actions'
			});
		},
		fbLike: function () {
			var that = this;
			FB.login(function(response) {
				if (response.authResponse) {
					that.fbAuth = response.authResponse;
					that.fbUserID = response.userID;
					FB.api(
						'https://graph.facebook.com/me/og.likes',
						'post', {
							object: '178935165464596',
							privacy: {
								'value': 'SELF'
							}
						},
						function (response) {
							console.log(response);
							if (!response) {
								alert('Error occurred.');
							} else if (response.error) {
								console.log(response);
							} else {
								console.log(response);
							}
						}
					);
				} else {
					// cancelled
					console.log('now I would aler the user if they don\'t log in you can\'t post');
				}
			}, {
				scope: 'email,publish_actions'
			});
		},
		emailPost: function () {
			var that = this,
				email = this.$userEmailInput.val(),
				message = this.$userPostTextarea.val();
			if (!email || !message || !RiotAPI.validateEmail(email)) {
				that.modalMessage('Not So Fast!', 'Make sure you fill in a valid email and message before you submit.');
				return false;
			}
			RiotAPI.call({
				data: {
					action: 'contactForm',
					user_email: email,
					message: message,
					contact_nonce: $('#contact_nonce').val()
				},
				after: function (msg) {
					if (msg === 'sent') {
						that.$userEmailInput.val('');
						that.$userPostTextarea.val('');
						that.modalMessage('Got It!', 'Thanks for the message. We will holla back when we get a chance.');
						return true;
					}
					that.modalMessage('What the ...?', 'There was an error in sending your form - ' + msg);
				}
			});
		},
		modalMessage: function (header, body) {
			this.$modal.find('h2').html(header);
			this.$modal.find('p').html(body);
			this.$modal.riotModal('show');
		},
		handleResize: function () {
			this.updateState = true;
		},
		toggle: function (e) {
			if (typeof e === 'object' && typeof e.preventDefault === 'function') {
				e.preventDefault();
			}
			this.updateState && this.getState();
			this.updateState = false;
			if (this.state === 'small') {
				$body.toggleClass('show-social');
			} else {
				$body.toggleClass('hide-social');
				if (Modernizr.touch) {
					$body.trigger('refreshScroll');
				}
			}
		},
		getState: function () {
			this.state = ($window.width() >= this.options.bpLarge) ? 'large' : 'small';
			return this.state;
		},
		_setupModal: function () {
			var that = this,
				tryRiotModal = function () {
					if (typeof $.fn.riotModal === 'function' && !that.$modal.data('riotModal')) {
						return that.$modal.riotModal();
					}
					//if it's not setup yet, try again in a second
					setTimeout(tryRiotModal, 1000);
				};
			tryRiotModal();
		}
	};
	//===jQuery Plugin
	$.fn[pluginName] = function (option) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data(pluginName),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data(pluginName, (data = new Socializer(this, options)));
			}
			if (typeof option === 'string') {
				data[option]();
			}
		});
	};
	$.fn[pluginName].defaults = {
		bpSmall:640,
		bpLarge:1024
	};
	//===Data-API
	$(function () {
		var js,
			id = 'facebook-jssdk',
			ref = document.getElementsByTagName('script')[0];
		$window = $(window);
		$body = $('body');
		window.fbAsyncInit = function () {
			FB.init({
				appId      : '201900699822506', // App ID
				channelUrl : $body.data('templateurl') + '/facebook-channel.php', // Channel File
				status     : false, // check login status
				cookie     : true, // enable cookies to allow the server to access the session
				xfbml      : true  // parse XFBML
			});
		};
		// Load the facebook SDK Asynchronously
		if (document.getElementById(id)) {
			return false;
		}
		js = document.createElement('script');
		js.id = id;
		js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
		ref.parentNode.insertBefore(js, ref);
		//init the socializer plugin
		$('#desktop-socializer').socializer();
	});
});