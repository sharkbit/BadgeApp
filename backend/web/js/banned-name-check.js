(function ($) {
	'use strict';

	function showDecisionDialog(message, confirmLabel) {
		const deferred = $.Deferred();
		const isConfirmation = Boolean(confirmLabel);
		const overlay = $('<div>', {
			css: {
				position: 'fixed',
				inset: 0,
				zIndex: 20000,
				display: 'flex',
				alignItems: 'center',
				justifyContent: 'center',
				padding: '20px',
				background: 'rgba(0, 0, 0, 0.55)'
			}
		});
		const dialog = $('<div>', {
			role: 'dialog',
			'aria-modal': 'true',
			'aria-labelledby': 'banned-name-dialog-title',
			css: {
				width: '100%',
				maxWidth: '440px',
				padding: '24px',
				borderRadius: '6px',
				background: '#fff',
				boxShadow: '0 8px 28px rgba(0, 0, 0, 0.3)',
				fontFamily: 'Arial, sans-serif'
			}
		});
		const title = $('<h2>', {
			id: 'banned-name-dialog-title',
			text: 'Please confirm',
			css: { margin: '0 0 14px', fontSize: '20px' }
		});
		const messageText = $('<p>', {
			text: message,
			css: { margin: '0 0 22px', lineHeight: '1.5' }
		});
		const buttons = $('<div>', {
			css: { display: 'flex', justifyContent: 'flex-end', gap: '10px' }
		});
		const cancelButton = $('<button>', {
			type: 'button',
			text: 'Cancel',
			css: {
				padding: '8px 16px',
				border: '1px solid #666',
				borderRadius: '4px',
				background: '#fff',
				color: '#222',
				cursor: 'pointer'
			}
		});
		let confirmButton = null;

		if (isConfirmation) {
			confirmButton = $('<button>', {
				type: 'button',
				text: confirmLabel,
				css: {
					padding: '8px 16px',
					border: '1px solid #a94442',
					borderRadius: '4px',
					background: '#c9302c',
					color: '#fff',
					cursor: 'pointer'
				}
			});
		}

		let isClosed = false;
		function close(confirmed) {
			if (isClosed) {
				return;
			}
			isClosed = true;
			$(document).off('keydown.bannedNameDialog');
			overlay.remove();
			deferred.resolve(confirmed);
		}

		cancelButton.on('click', function () {
			close(false);
		});
		buttons.append(cancelButton);
		if (confirmButton) {
			confirmButton.on('click', function () {
				close(true);
			});
			buttons.append(confirmButton);
		}
		dialog.append(title, messageText, buttons);
		overlay.append(dialog).appendTo('body');
		cancelButton.trigger('focus');
		$(document).on('keydown.bannedNameDialog', function (event) {
			if (event.key === 'Escape') {
				event.preventDefault();
				close(false);
			} else if (event.key === 'Enter') {
				event.preventDefault();
				close(Boolean(confirmButton && document.activeElement === confirmButton[0]));
			}
		});

		return deferred.promise();
	}

	function showNotice(message) {
		showDecisionDialog(message, null);
	}

	function showConfirmation(message, confirmLabel) {
		return showDecisionDialog(message, confirmLabel);
	}

	window.agcDecisionDialog = {
		confirm: showConfirmation,
		notice: showNotice
	};

	$(function () {
		$('form[data-banned-name-check-url]').each(function () {
			const form = $(this);
			const firstName = $(form.data('banned-name-first-name'));
			const lastName = $(form.data('banned-name-last-name'));
			const status = $(form.data('banned-name-status'));
			const override = $(form.data('banned-name-override'));
			const declineUrl = form.data('banned-name-decline-url');
			const agreeButton = form.find('#guest-agree');
			const requiresNameCheck = agreeButton.prop('disabled');
			let checkState = 'unchecked';
			let checkedName = '';
			let requestPending = false;
			let requestId = 0;
			let submitAfterCheck = false;

			function updateAgreeButton() {
				if (requiresNameCheck) {
					agreeButton.prop('disabled', checkState !== 'allowed' || checkedName !== currentName());
				}
			}

			function currentName() {
				return $.trim(firstName.val() || '') + '\u0000' + $.trim(lastName.val() || '');
			}

			updateAgreeButton();

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
					updateAgreeButton();
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
						updateAgreeButton();
						return;
					}
					if (response.banned) {
						if (response.canOverride) {
							showConfirmation(response.msg, 'Override').done(function (confirmed) {
								if (currentRequest !== requestId || name !== currentName()) {
									return;
								}
								if (confirmed) {
									override.val('1');
									checkState = 'allowed';
									status.removeClass('text-danger').addClass('text-success').text('Override confirmed. You may continue.');
									updateAgreeButton();
									resumeSubmit();
								} else if (declineUrl) {
									window.location.href = declineUrl;
								} else {
									checkState = 'blocked';
									submitAfterCheck = false;
									status.removeClass('text-success').addClass('text-danger').text('Please See Office Staff');
									updateAgreeButton();
								}
							});
						} else {
							checkState = 'blocked';
							submitAfterCheck = false;
							status.removeClass('text-success').addClass('text-danger').text('Please See Office Staff');
							updateAgreeButton();
						}
						return;
					}
					checkState = 'allowed';
					status.removeClass('text-danger').addClass('text-success').text('Name checked.');
					updateAgreeButton();
					resumeSubmit();
				}).fail(function () {
					if (currentRequest !== requestId) {
						return;
					}
					requestPending = false;
					checkState = 'blocked';
					submitAfterCheck = false;
					status.removeClass('text-success').addClass('text-danger').text('Could not check the name. Please try again.');
					updateAgreeButton();
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
				updateAgreeButton();
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
					submitAfterCheck = false;
					override.val('0');
					status.empty();
					resumeSubmit();
					return;
				}

				checkState = 'checking';
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
						checkState = 'unchecked';
						return;
					}
					if (!response.success) {
						checkState = 'blocked';
						submitAfterCheck = false;
						const message = response.msg || 'Could not check badge eligibility.';
						status.removeClass('text-success').addClass('text-danger').text(message);
						showNotice(message);
						return;
					}
					if (response.requiresConfirmation) {
						showConfirmation(response.msg, 'Override').done(function (confirmed) {
							if (currentRequest !== requestId || badgeNumber !== currentBadge()) {
								return;
							}
							if (!confirmed) {
								checkState = 'blocked';
								submitAfterCheck = false;
								if (declineUrl) {
									window.location.href = declineUrl;
								} else {
									status.removeClass('text-success').addClass('text-danger').text('Please See Office Staff');
								}
								return;
							}
							override.val('1');
							status.removeClass('text-danger').addClass('text-success').text('Override confirmed. You may continue.');
							checkState = 'allowed';
							resumeSubmit();
						});
						return;
					}
					status.removeClass('text-danger').addClass('text-success').text('Badge eligibility checked.');
					checkState = 'allowed';
					resumeSubmit();
				}).fail(function () {
					if (currentRequest !== requestId) {
						return;
					}
					checkState = 'blocked';
					submitAfterCheck = false;
					status.removeClass('text-success').addClass('text-danger').text('Could not check badge eligibility. Please try again.');
				});
			}

			badgeInput.on('input', function () {
				requestId++;
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
				if (checkState === 'checking') {
					submitAfterCheck = true;
					return false;
				}
				if (checkState === 'blocked') {
					status.removeClass('text-success').addClass('text-danger')
						.text('Change the badge number and leave the field to check eligibility.');
					return false;
				}
				submitAfterCheck = true;
				checkBadge();
				return false;
			});

		});
	});
})(jQuery);
