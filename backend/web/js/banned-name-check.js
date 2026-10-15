(function ($) {
	'use strict';

	$(function () {
		$('form[data-banned-name-check-url]').each(function () {
			const form = $(this);
			const firstName = $(form.data('banned-name-first-name'));
			const lastName = $(form.data('banned-name-last-name'));
			const status = $(form.data('banned-name-status'));
			const override = $(form.data('banned-name-override'));
			const declineUrl = form.data('banned-name-decline-url');
			let checkState = 'unchecked';
			let checkedName = '';
			let requestPending = false;
			let requestId = 0;
			let submitAfterCheck = false;

			function currentName() {
				return $.trim(firstName.val() || '') + '\u0000' + $.trim(lastName.val() || '');
			}

			function resumeSubmit() {
				if (!submitAfterCheck) {
					return;
				}
				submitAfterCheck = false;
				if (form[0].requestSubmit) {
					form[0].requestSubmit();
				} else {
					form.trigger('submit');
				}
			}

			function checkName(force) {
				const first = $.trim(firstName.val() || '');
				const last = $.trim(lastName.val() || '');
				const name = first + '\u0000' + last;
				if (!first || !last) {
					checkState = 'unchecked';
					checkedName = '';
					submitAfterCheck = false;
					override.val('0');
					status.empty();
					return;
				}
				if (!force && checkState !== 'unchecked' && checkedName === name) {
					return;
				}

				requestPending = true;
				checkState = 'checking';
				checkedName = name;
				const currentRequest = ++requestId;
				override.val('0');
				status.removeClass('text-danger text-success').text('Checking name...');

				const data = {
					first_name: first,
					last_name: last
				};
				const csrfParam = $('meta[name="csrf-param"]').attr('content');
				const csrfToken = $('meta[name="csrf-token"]').attr('content');
				if (csrfParam && csrfToken) {
					data[csrfParam] = csrfToken;
				}

				$.ajax({
					method: 'POST',
					url: form.data('banned-name-check-url'),
					data: data,
					dataType: 'json'
				}).done(function (response) {
					if (currentRequest !== requestId) {
						return;
					}
					if (name !== currentName()) {
						requestPending = false;
						checkState = 'unchecked';
						return;
					}
					requestPending = false;
					if (!response.success) {
						checkState = 'blocked';
						submitAfterCheck = false;
						status.addClass('text-danger').text(response.msg || 'Could not check the name.');
						return;
					}
					if (response.banned) {
						if (response.canOverride && window.confirm(response.msg)) {
							override.val('1');
							checkState = 'allowed';
							status.removeClass('text-danger').addClass('text-success').text('Override confirmed. You may continue.');
							resumeSubmit();
						} else if (response.canOverride && declineUrl) {
							window.location.href = declineUrl;
						} else {
							checkState = 'blocked';
							submitAfterCheck = false;
							status.removeClass('text-success').addClass('text-danger').text('Please See Office Staff');
						}
						return;
					}
					checkState = 'allowed';
					status.removeClass('text-danger').addClass('text-success').text('Name checked.');
					resumeSubmit();
				}).fail(function () {
					if (currentRequest !== requestId) {
						return;
					}
					requestPending = false;
					checkState = 'blocked';
					submitAfterCheck = false;
					status.removeClass('text-success').addClass('text-danger').text('Could not check the name. Please try again.');
				});
			}

			firstName.add(lastName).on('input', function () {
				requestId++;
				requestPending = false;
				checkState = 'unchecked';
				checkedName = '';
				submitAfterCheck = false;
				override.val('0');
				status.empty();
			}).on('blur', function () {
				checkName(false);
			});

			form.on('submit', function (event) {
				if (checkState === 'blocked') {
					event.preventDefault();
					return false;
				}
				if (checkState !== 'allowed' || checkedName !== currentName() || requestPending) {
					event.preventDefault();
					submitAfterCheck = true;
					checkName(true);
					return false;
				}
			});
		});
	});
})(jQuery);
