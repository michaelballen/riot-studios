//Shopping Cart 0.0
/*global RiotAPI*/
define(['jquery', 'plugins/riot-api', 'plugins/riot-modal', 'vendor/validation/jquery.validate.min'], function ($) {
	"use strict";
	//definitions
	var $emailForm;
	$(function () {
		$emailForm = $('#apply-form');
		$emailForm.validate({
			rules: {
				user_email: {
					email:true,
					required:true
				}
			},
			submitHandler: function(f) {
				if (!$emailForm.valid()) {
					return false;
				}
				//see if this is a new app or an old
				RiotAPI.call({
					data: {
						action:'getJobAppID',
						user_email: $('input[name="user_email"]').val()
					},
					after: function (r) {
						console.log(r);
						if (r === '0' || r === 0 || $('input[name="secret"]').length > 0) {
							f.submit();
							return true;
						}
						$('body').one('click.riotJobApplication.resendEmail', '#resend-job-application-link', function (e) {
							var $btn = $(this).addClass('btn-warning').html('Sending Link &hellip;');
							RiotAPI.call({
								data: {
									action:'resendJobAppLink',
									app_id: r
								},
								after: function (r) {
									console.log(r);
									if (r === true) {
										$btn.addClass('btn-success').html('<span class="ifontcheckmark"></span> Link Sent');
										return true;
									}
									$btn.removeClass('btn-warning').addClass('btn-danger').html('Error. Try again later.');
								}
							});
							e.preventDefault();
						});
						$.showModal("You've already started applying", "Looks like someone has already started applying with that email. Check your email for a special link to pick up where you left off. Click below to resend the link.<br><br><button id='resend-job-application-link' href='#' class='btn' style='width:100%; padding:10px;'>Re-Send Link &raquo;</button>");
					}
				});
			}
		});
	});
});