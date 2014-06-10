/*global RiotAPI, Modernizr*/
require(['jquery', 'vendor/bootstrap/bootstrap-transition', 'plugins/riot-api', 'vendor/validation/jquery.validate.min'], function ($) {
	var $signupInput,
		$signupForm,
		$signupButton,
		$body,
		$modal,
		$teamModal,
		$teamLink,
		$html,
		submitting = false,
		showModal = function () {
			$html.addClass('modal-on');
			$modal[0].offsetWidth;
			$modal.addClass('show');
		},
		showTeamModal = function () {
			$html.addClass('team-modal-on');
			$teamModal[0].offsetWidth;
			$teamModal.addClass('show');
		},
		hideModal = function (e) {
			e.preventDefault();
			if (Modernizr.csstransitions) {
				$modal.one($.support.transition.end, function () {
					$html.removeClass('modal-on');
				}).removeClass('show');
				return true;
			}
			$modal.removeClass('show');
			$html.removeClass('modal-on');
		},
		hideTeamModal = function (e) {
			var scroll=0;
			e.preventDefault();
			scroll = $(window).scrollTop();
			window.location.hash='';
			$(window).scrollTop(scroll);
			$('.person.active').removeClass('active');
			if (Modernizr.csstransitions) {
				$teamModal.one($.support.transition.end, function () {
					$html.removeClass('team-modal-on');
				}).removeClass('show');
				return true;
			}
			$teamModal.removeClass('show');
			$html.removeClass('team-modal-on');
		},
		handleSubmit = function (e) {
			if (submitting) {
				return false;
			}
			if (!$signupForm.valid()) {
				return false;
			}
			submitting = true;
			$signupButton.removeClass('btn-success').addClass('btn-warning');
			RiotAPI.call({
				data: {
					action:'registerUser',
					user_email: $signupInput.val(),
					group_name: 'Fan of',
					group_value: 'Believe Me'
				},
				after: function (m, status) {
					if (status === 'api-error') {
						submitting = false;
						alert(m);
						return false;
					}
					showModal();
					$signupButton.removeClass('btn-warning').addClass('btn-success sent');
				}
			});
			e.preventDefault();
		};
	$(function () {
		var $currentHash;
		$body = $('body');
		$html = $('html');
		$modal = $('#bm-modal');
		$teamModal = $('#team-modal');
		$teamLink = $('.person').on('click.believeMe', function (e) {
			var $this = $(this),
				title,
				body,
				img,
				note;
			e.preventDefault();
			if ($this.hasClass('active')) {
				return false;
			}
			$this.addClass('active');
			title = $this.find('h3').html();
			body = $this.find('.content').html();
			img = $this.find('img')[0].src;
			note = $this.find('.note').html();
			//populate the team modal box
			$teamModal.find('.modal-inner img').attr({
				src: img,
				title: title
			});
			$teamModal.find('h2').html(title);
			$teamModal.find('.content').html(body);
			$teamModal.find('.note').html(note);
			showTeamModal();
			window.location.hash = $this.attr('href');
		});
		$modal.find('.close-btn').on('click.believeMe', hideModal);
		$teamModal.find('.close-btn').on('click.believeMe', hideTeamModal);
		$signupForm = $('#signup-form').on('submit.believeMe', handleSubmit);
		$signupForm.validate({
			errorLabelContainer: "#signup-error-label",
			rules: {
				user_email: {
					required: true,
					email: true
				}
			},
			messages: {
				user_email: {
					required: 'Email is required',
					email: 'This email looks a little off'
				}
			}
		});
		$signupButton = $signupForm.find('button').on('click.believeMe', handleSubmit);
		$signupInput = $('#signup-input');
		$currentHash = $('.person[href="#' + window.location.hash.replace(/^#/, '') + '"]');
		if ($currentHash.length > 0) {
			$currentHash.trigger('click.believeMe');
		}
	});
});