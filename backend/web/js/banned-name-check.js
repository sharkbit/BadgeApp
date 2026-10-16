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

		$('form[data-badge-eligibility-check-url]').each(function () {
			const form = $(this);
			const badgeInput = $(form.data('badge-eligibility-input'));
			const status = $(form.data('badge-eligibility-status'));
			const override = $(form.data('badge-eligibility-override'));
			const declineUrl = form.data('badge-eligibility-decline-url');
			let checkState = 'unchecked';
			let checkedBadge = '';
			let requestPending = false;
			let requestId = 0;
			let submitAfterCheck = false;

			function currentBadge() {
				return $.trim(badgeInput.val() || '');
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

			function checkBadge() {
				const badgeNumber = currentBadge();
				if (!badgeNumber) {
					checkState = 'allowed';
					checkedBadge = '';
					requestPending = false;
					submitAfterCheck = false;
					override.val('0');
					status.empty();
					resumeSubmit();
					return;
				}

				checkState = 'checking';
				requestPending = true;
				checkedBadge = badgeNumber;
				const currentRequest = ++requestId;
				override.val('0');
				status.removeClass('text-danger text-success').text('Checking badge eligibility...');

				const data = { badge_number: badgeNumber };
				const csrfParam = $('meta[name="csrf-param"]').attr('content');
				const csrfToken = $('meta[name="csrf-token"]').attr('content');
				if (csrfParam && csrfToken) {
					data[csrfParam] = csrfToken;
				}

				$.ajax({
					method: 'POST',
					url: form.data('badge-eligibility-check-url'),
					data: data,
					dataType: 'json'
				}).done(function (response) {
					if (currentRequest !== requestId) {
						return;
					}
					if (badgeNumber !== currentBadge()) {
						requestPending = false;
						checkState = 'unchecked';
						return;
					}
					requestPending = false;
					if (!response.success) {
						checkState = 'blocked';
						submitAfterCheck = false;
						const message = response.msg || 'Could not check badge eligibility.';
						status.removeClass('text-success').addClass('text-danger').text(message);
						window.alert(message);
						return;
					}
					if (response.requiresConfirmation && !window.confirm(response.msg)) {
						checkState = 'blocked';
						submitAfterCheck = false;
						if (declineUrl) {
							window.location.href = declineUrl;
						} else {
							status.removeClass('text-success').addClass('text-danger').text('Please See Office Staff');
						}
						return;
					}
					if (response.requiresConfirmation) {
						override.val('1');
						status.removeClass('text-danger').addClass('text-success').text('Override confirmed. You may continue.');
					} else {
						status.removeClass('text-danger').addClass('text-success').text('Badge eligibility checked.');
					}
					checkState = 'allowed';
					resumeSubmit();
				}).fail(function () {
					if (currentRequest !== requestId) {
						return;
					}
					requestPending = false;
					checkState = 'blocked';
					submitAfterCheck = false;
					status.removeClass('text-success').addClass('text-danger').text('Could not check badge eligibility. Please try again.');
				});
			}

			badgeInput.on('input', function () {
				requestId++;
				requestPending = false;
				checkState = 'unchecked';
				checkedBadge = '';
				submitAfterCheck = false;
				override.val('0');
				status.empty();
			}).on('change', checkBadge);

			form.on('submit', function (event) {
				const badgeNumber = currentBadge();
				if (!badgeNumber || (checkState === 'allowed' && checkedBadge === badgeNumber)) {
					return;
				}
				event.preventDefault();
				if (checkState === 'checking' && requestPending) {
					submitAfterCheck = true;
					return false;
				}
				if (checkState === 'blocked') {
					status.removeClass('text-success').addClass('text-danger')
						.text('Change the badge number and leave the field to check eligibility.');
					return false;
				}
				status.removeClass('text-success').addClass('text-danger')
					.text('Change the badge number and leave the field to check eligibility.');
				return false;
			});

		});
	});
})(jQuery);
