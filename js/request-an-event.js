/*global RiotAPI*/
define(['jquery', 'plugins/riot-api', 'vendor/validation/jquery.validate.min'], function ($) {
	var $requestEventForm,
		$submitBtn,
		submitting = false,
		submitForm = function (e) {
			e.preventDefault();
			if (submitting) {
				return false;
			}
			if(!$requestEventForm.valid()) {
				alert('Please fix form field errors before submitting');
				return false;
			}
			submitting = true;
			$submitBtn.removeClass('btn-primary btn-success').addClass('btn-warning');
			RiotAPI.call({
				data: $requestEventForm.serialize(),
				after: function (m, status) {
					if (status !== 'api-error') {
						$submitBtn.removeClass('btn-primary btn-warning').addClass('btn-success');
					}
				}
			});
		};
	$(function () {
		$requestEventForm = $('#request-event-form').on('submit.requestEvent', submitForm);
		$requestEventForm.validate({
			rules: {
				user_name: {
					required:true
				},
				user_email: {
					required: true,
					email: true
				}
			}
		});
		$submitBtn = $('#request-event-submit').on('click.requestEvent', submitForm);
	});
});