//Riot Job App 0.0
/*global RiotAPI*/
define(['jquery', 'plugins/riot-api', 'plugins/riot-plugins', 'vendor/bootstrap/bootstrap-transition', 'vendor/validation/jquery.validate.min', 'vendor/validation/additional-methods.min'], function ($) {
	"use strict";
	//definitions
	var rJA,
		RiotJobApp = function () {
			var that = this;
			this.hasChange = false;
			this.saveTimeout = 30000;
			this.$saveButton = $('#save-container').on('click.riotJobApp', $.proxy(this.saveApp, this));
			this.$form = $('#job-application-form').on('submit.riotJobApp', $.proxy(this.saveApp, this));
			this.$inputs = this.$form.find('input, select, textarea').on('change.riotJobApp.trackChange', function () {
				that.hasChange = true;
			});
			this.$submit = $('#application-submit').on('click.riotJobApp', $.proxy(this._handleSubmitClick, this));
			this._setupEducationSection();
			this._setupPositionChecks();
			this._setupReferencesUI();
			this._setupResume();
			this._setupSocial();
			this._setupValidation();
			this._setupSave();
		};
	RiotJobApp.prototype = {
		_setupValidation: function () {
			var validation = {
				rules: {
					resume: {
						required: true,
						accept: 'application/pdf,application/doc'
					},
					user_first_name: {
						required: true
					},
					user_last_name: {
						required: true
					}
				}
			};
			if (typeof $.fn.validate === 'function' && $.validator.methods.hasOwnProperty('accept')) {
				return this.$form.validate(validation);
			}
			setTimeout($.proxy(this._setupValidation, this), 100);
		},
		_setupSocial: function () {
			$('#facebook-input').on('change.riotJobApp.url', function () {
				var $this = $(this),
					val = $this.val(),
					newVal = '';
				if (/https?:\/\//.exec(val) === null) {
					newVal += 'http://';
				}
				if (/facebook.com\//.exec(val) === null) {
					newVal += 'facebook.com/';
				}
				newVal += val;
				if (newVal !== val) {
					$this.val(newVal);
				}
			});
			$('#twitter-input').on('change.riotJobApp.url', function () {
				var $this = $(this),
					val = $this.val(),
					newVal = val.replace(/https?:\/\//, '').replace(/twitter.com\//, '').replace('@', '');
				newVal = '@' + newVal;
				if (newVal !== val) {
					return $this.val(newVal);
				}
			});
		},
		_setupEducationSection: function () {
			var that = this;
			this.undergradInputGroup = $('#undergrad-input-group');
			this.mastersInputGroup = $('#masters-input-group');
			$('#education-acquired').on('change.riotJobApp', function () {
				var level = $(this).val();
				switch (level) {
					case 'Some College':
						that.undergradInputGroup.find('label[for="college"]').html('Currently Attending');
						that.undergradInputGroup.find('label[for="college_degree"]').html('Studying');
						that.undergradInputGroup.removeClass('hidden');
						that.mastersInputGroup.addClass('hidden');
						break;
					case 'Undergrad Degree':
						that.undergradInputGroup.find('label[for="college"]').html('Earned Undergrad From');
						that.undergradInputGroup.find('label[for="college_degree"]').html('In');
						that.undergradInputGroup.removeClass('hidden');
						that.mastersInputGroup.addClass('hidden');
						break;
					case 'Masters Degree':
						that.undergradInputGroup.find('label[for="college"]').html('Earned Undergrad From');
						that.undergradInputGroup.find('label[for="college_degree"]').html('In');
						that.undergradInputGroup.removeClass('hidden');
						that.mastersInputGroup.removeClass('hidden');
						break;
					default:
						that.undergradInputGroup.addClass('hidden');
						that.mastersInputGroup.addClass('hidden');
				}
			});
		},
		_setupPositionChecks: function () {
			var that = this,
				toggleCheck = function (e) {
					var $this = $(this).toggleClass('checked'),
						newVal;
					if ($this.hasClass('checked')) {
						newVal = '1';
					} else {
						newVal = '0';
					}
					$this.parents('li').find('input').val(newVal);
					that.hasChange = true;
					e.preventDefault();
				};
			$('#positions-list').find('li a.box').on('click.riotJobApp', toggleCheck);
		},
		_setupReferencesUI: function () {
			var showFocus = function () {
					$(this).parents('li').addClass('focused');
				},
				hideFocus = function () {
					$(this).parents('li').removeClass('focused');
				},
				referenceItems = $('#reference-list li');
			referenceItems.find('input').on('focus.riotJobApp', showFocus).on('blur.riotJobApp', hideFocus);
		},
		_setupResume: function () {
			var that = this,
				$resumeTarget = $('#resume_upload_target'),
				$resumeHiddenInput = $('input[name="resume"]'),
				checkIframe = function () {
					var content = $resumeTarget.contents().find('body').html();
					if (content === '') {
						return setTimeout(checkIframe, 1000);
					}
					$resumeTarget.contents().find('body').html('');
					content = $.parseJSON(content);
					$resumeHiddenInput.val(content.data);
					that.resumeVal = content.data;
					$button.removeClass('btn-primary btn-warning').addClass('btn-success');
				},
				onChange = function () {
					var val = $(this).val();
					if (val !== '') {
						if (val !== that.resumeVal) {
							$button.removeClass('btn-primary btn-success').addClass('btn-warning');
							that.resumeVal = val;
							that.$form.attr({
								action: RiotAPI.rootURL + '/api?action=uploadResume',
								target: 'resume_upload_target',
								method: 'post',
								enctype: 'multipart/form-data'
							});
							that.$form[0].submit();
							that.$form.removeAttr('target');
							that.$form.removeAttr('action');
							that.$form.removeAttr('enctype');
							checkIframe();
						}
					}
				},
				$resumeInput = $('#resume-input').on('change.riotJobApp', onChange),
				$button = $resumeInput.parent();
			this.resumeVal = $resumeInput.val();
		},
		_handleSubmitClick: function (e) {
			var that = this,
				confirm;
			e.preventDefault();
			confirm = window.confirm("Are you sure you'd like to submit your application?");
			if (confirm !== true) {
				return false;
			}
			this.$form.find('input[name="status"]').val('complete');
			this.hasChange = true;
			this.$submit.addClass('btn-warning').removeClass('btn-success btn-primary');
			this.$form.one('saved.submitClick', function () {
				that.$submit.removeClass('btn-warning').addClass('btn-success');
			});
			this.$saveButton.trigger('click');
		},
		saveApp: function (e) {
			var that = this,
				$formData;
			if (e) {
				e.preventDefault();
			}
			if (this.saving) {
				return false;
			}
			if (!e && !this.hasChange) {
				setTimeout($.proxy(that._setupSave, that), 3000);
				return false;
			}
			if (typeof e === 'object' && !this.hasChange) {
				//pretend we're saving...
				that._showSaved();
				setTimeout($.proxy(that._setupSave, that), 3000);
				return true;
			}
			clearTimeout(this.saveTO);
			this._showSaving();
			this.hasChange = false;
			$formData = $.extend({
				type: "POST",
				action: 'saveJobApplication'
			}, this._collectFormData());
			RiotAPI.call({
				data: $formData,
				after: function (r) {
					if (r === 'saved') {
						that._showSaved();
						setTimeout($.proxy(that._setupSave, that), 3000);
						return true;
					}
					that.hasChange = true;
					that._showSaveError();
				}
			});
		},
		_setupSave: function () {
			if (Modernizr.csstransitions && (this.$saveButton.hasClass('saved') || this.$saveButton.hasClass('saved'))) {
				this.$saveButton.one($.support.transition.end, function () {
					this.offsetWidth;
				});
			}
			this.$saveButton.removeClass('saved saving');
			this.saveTO = setTimeout($.proxy(this.saveApp, this), this.saveTimeout);
		},
		_showSaving: function () {
			this.$saveButton.removeClass('saved error').addClass('saving');
		},
		_showSaveError: function () {
			this.$saveButton.removeClass('saved saving').addClass('error');
		},
		_showSaved: function () {
			this.$form.trigger('saved');
			this.$saveButton.removeClass('saving error').addClass('saved');
		},
		_collectFormData: function () {
			var $obj = {};
			$.map(this.$inputs, function (n) {
				if ($(n).prop('tagName').toLowerCase() === 'textarea') {
					$obj[n.name] = n.value.replace( /\r?\n/g, "<br>" );
				} else {
					$obj[n.name] = $(n).val();
				}
			});
			return $obj;
		}
	};
	//===Data-API
	$(function () {
		if ($('#job-application-form').data('read_only') !== 1) {
			rJA = new RiotJobApp();
		}
	});
});