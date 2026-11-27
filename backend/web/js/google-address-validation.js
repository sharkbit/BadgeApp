(function ($) {
	'use strict';

	$(document).on('click', '.js-google-address-validate', function () {
		var $button = $(this);
		var $panel = $button.closest('.google-address-validation');
		var $form = $button.closest('form');
		var $status = $panel.find('.js-google-address-status');
		var $applyButton = $panel.find('.js-google-address-apply');
		var formData = {
			street: $form.find($button.data('street')).val(),
			zip: $form.find($button.data('zip')).val(),
			city: $form.find($button.data('city')).val(),
			state: $form.find($button.data('state')).val()
		};
		var $csrf = $form.find('input[name^="_csrf"]').first();

		if ($csrf.length) {
			formData[$csrf.attr('name')] = $csrf.val();
		} else {
			var csrfParam = $('meta[name="csrf-param"]').attr('content');
			if (csrfParam) {
				formData[csrfParam] = $('meta[name="csrf-token"]').attr('content');
			}
		}

		$button.prop('disabled', true);
		$applyButton.hide().removeData('postal-address');
		$status.text('Validating address...');

		$.ajax({
			url: $button.data('url'),
			method: 'POST',
			dataType: 'json',
			data: formData
		}).done(function (response) {
			var postalAddress = response.postalAddress || {};
			var lines = postalAddress.addressLines || [];
			var displayAddress = (response.formattedAddress || 'Google did not return a standardized address.')
				.replace(/\b(\d{5})-\d{4}\b/g, '$1');
			var enteredZip = String(formData.zip || '').replace(/\D/g, '').substring(0, 5);
			var suggestedZip = String(postalAddress.postalCode || '').replace(/\D/g, '').substring(0, 5);
			var message;

			if (enteredZip && suggestedZip && enteredZip === suggestedZip) {
				message = 'ZIP matches on the first five digits. ';
				message += response.verdict && response.verdict.addressComplete
					? 'Address appears complete: '
					: "Google's address suggestion: ";
				message += displayAddress;
			} else if (enteredZip && suggestedZip) {
				message = 'ZIP mismatch: entered ' + enteredZip + ', Google suggests ' + suggestedZip + '. ' + displayAddress;
			} else if (response.verdict && response.verdict.addressComplete) {
				message = 'Address appears complete: ' + displayAddress;
			} else {
				message = "Please review Google's address result: " + displayAddress;
			}
			$status.text(message);

			if (lines.length) {
				var addressDiffers = normalizeAddressValue(lines.join(' ')) !== normalizeAddressValue(formData.street);
				if (postalAddress.locality) {
					addressDiffers = addressDiffers
						|| normalizeAddressValue(postalAddress.locality) !== normalizeAddressValue(formData.city);
				}
				if (postalAddress.administrativeArea) {
					var $state = $form.find($button.data('state'));
					var $matchingState = $state.is('select')
						? $state.find('option').filter(function () {
							return normalizeAddressValue(this.value) === normalizeAddressValue(postalAddress.administrativeArea)
								|| normalizeAddressValue($(this).text()) === normalizeAddressValue(postalAddress.administrativeArea);
						}).first()
						: $();
					var suggestedState = $matchingState.length ? $matchingState.val() : postalAddress.administrativeArea;
					addressDiffers = addressDiffers
						|| normalizeAddressValue(suggestedState) !== normalizeAddressValue(formData.state);
				}
				if (suggestedZip && enteredZip !== suggestedZip) {
					addressDiffers = true;
				}

				if (addressDiffers) {
					$applyButton.data('postal-address', postalAddress).show();
				}
			}
		}).fail(function (xhr) {
			var response = xhr.responseJSON || {};
			$status.text(response.message || 'Address validation failed.');
		}).always(function () {
			$button.prop('disabled', false);
		});
	});

	function normalizeAddressValue(value) {
		return String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();
	}

	$(document).on('click', '.js-google-address-apply', function () {
		var $button = $(this);
		var $panel = $button.closest('.google-address-validation');
		var $form = $button.closest('form');
		var address = $button.data('postal-address') || {};
		var values = {
			street: (address.addressLines || []).join('\n'),
			city: address.locality || '',
			state: address.administrativeArea || '',
			zip: (address.postalCode || '').substring(0, 5)
		};

		['street', 'city', 'state', 'zip'].forEach(function (field) {
			var $input = $form.find($panel.find('.js-google-address-validate').data(field));
			if (!$input.length || !values[field]) {
				return;
			}

			if (field === 'state' && $input.is('select')) {
				var $option = $input.find('option').filter(function () {
					return this.value.toLowerCase() === values.state.toLowerCase()
						|| $(this).text().toLowerCase() === values.state.toLowerCase();
				}).first();
				if ($option.length) {
					$input.val($option.val()).trigger('change');
				}
				return;
			}

			$input.val(values[field]).trigger('change');
		});

		$panel.find('.js-google-address-status').text("Google's suggested address was applied. Review it before submitting.");
		$button.hide().removeData('postal-address');
	});
})(jQuery);
