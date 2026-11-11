(function (root, factory) {
	const logic = factory();
	if (typeof module === 'object' && module.exports) {
		module.exports = logic;
	}
	if (root) {
		root.BadgeFormLogic = logic;
	}
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
	'use strict';

	function roundCurrency(value) {
		return Number((Math.round((Number(value) || 0) * 100) / 100).toFixed(2));
	}

	function normalizeZeroValue(value) {
		return String(value) === '0' ? '' : value;
	}

	function calculateCart(items, badgeFee, discountOptions, clampZeroWhenNonPositive) {
		let itemTotal = 0;
		let taxTotal = 0;
		let total = 0;
		const cart = [];
		const prices = [];

		items.forEach(function (item) {
			const quantity = Number(item.qty);
			if (quantity > 0) {
				const itemAmount = roundCurrency(Number(item.ea) * quantity);
				const taxAmount = roundCurrency(Number(item.ea) * quantity * Number(item.taxRate));
				const price = roundCurrency(itemAmount + taxAmount);
				prices.push(price.toFixed(2));
				itemTotal += itemAmount;
				taxTotal += taxAmount;
				total += price;
				cart.push({
					item: item.item,
					sku: item.sku,
					ea: item.ea,
					qty: item.qty,
					price: price.toFixed(2),
				});
			} else {
				prices.push(null);
			}
		});

		const discount = discountOptions.reduce(function (sum, optionValue) {
			const value = String(optionValue);
			if (value.length <= 2) {
				return sum;
			}
			const amount = Number(value.split(':')[1]);
			return sum + (Number.isFinite(amount) ? amount : 0);
		}, 0);

		let amountDue = Number(badgeFee) - discount + total;
		const setCash = clampZeroWhenNonPositive ? amountDue <= 0 : amountDue < 0;
		if (setCash) {
			amountDue = 0;
		}

		return {
			cart: cart,
			prices: prices,
			itemTotal: roundCurrency(itemTotal),
			taxTotal: roundCurrency(taxTotal),
			total: roundCurrency(total),
			discount: roundCurrency(discount),
			amountDue: roundCurrency(amountDue),
			setCash: setCash,
		};
	}

	function titleCase(value) {
		return String(value).toLowerCase().split(' ').map(function (word) {
			return word.charAt(0).toUpperCase() + word.slice(1);
		}).join(' ');
	}

	function formatCardNumber(value) {
		const input = String(value).replace(/\D/g, '').substring(0, 16);
		if (input.length < 5) {
			return input;
		}
		if (input.length < 9) {
			return input.substring(0, 4) + '-' + input.substring(4, 8);
		}
		if (input.length < 13) {
			return input.substring(0, 4) + '-' + input.substring(4, 8) + '-' + input.substring(8, 12);
		}
		return input.substring(0, 4) + '-' + input.substring(4, 8) + '-'
			+ input.substring(8, 12) + '-' + input.substring(12, 16);
	}

	function getDriverLicenseField(scan, tag) {
		const marker = scan.indexOf(tag);
		if (marker < 1) {
			return null;
		}
		const start = marker + tag.length;
		const end = scan.indexOf('ArrowDown', start);
		return end < 0 ? null : scan.slice(start, end);
	}

	function parseDriverLicense(scan) {
		let firstName;
		let middleName;
		let lastName;
		const first = getDriverLicenseField(scan, 'DAC');
		if (first !== null) {
			const middle = getDriverLicenseField(scan, 'DAD');
			const last = getDriverLicenseField(scan, 'DCS');
			if (middle === null || last === null) {
				return null;
			}
			firstName = titleCase(first);
			middleName = middle.charAt(0).toUpperCase();
			lastName = titleCase(last);
		} else {
			const fullName = getDriverLicenseField(scan, 'DAA');
			if (fullName === null) {
				return null;
			}
			const parts = fullName.split(',');
			if (parts.length < 3) {
				return null;
			}
			lastName = titleCase(parts[0]);
			firstName = titleCase(parts[1]);
			middleName = parts[2].charAt(0).toUpperCase();
		}

		const values = {
			firstName: firstName + ' ' + middleName,
			lastName: lastName,
		};
		const dob = getDriverLicenseField(scan, 'DBB');
		if (dob !== null) {
			let year = dob.substring(0, 4);
			const month = dob.substring(4, 6);
			const day = dob.substring(6, 8);
			if (Number(month) > 12 || Number(day) > 31 || Number(year) < 1900) {
				year = dob.substring(4, 8);
			}
			values.yob = year;
		}
		const address = getDriverLicenseField(scan, 'DAG');
		if (address !== null) {
			values.address = titleCase(address);
		}
		const city = getDriverLicenseField(scan, 'DAI');
		if (city !== null) {
			values.city = titleCase(city);
		}
		const state = getDriverLicenseField(scan, 'DAJ');
		if (state !== null) {
			values.state = state;
		}
		const zip = getDriverLicenseField(scan, 'DAK');
		if (zip !== null) {
			values.zip = zip.substring(0, 5);
		}
		return values;
	}

	function parseSwipe(scan, currentYear) {
		if (typeof scan !== 'string') {
			return null;
		}
		if (scan.includes('ANSI 6360') || scan.includes('AAMVA6360')) {
			const fields = parseDriverLicense(scan);
			return fields === null ? null : { type: 'license', fields: fields };
		}

		const cardMatch = scan.match(/[Bb]\d{16}/);
		if (!cardMatch) {
			return null;
		}
		const firstCaret = scan.indexOf('^', cardMatch.index);
		const secondCaret = firstCaret < 0 ? -1 : scan.indexOf('^', firstCaret + 1);
		if (secondCaret < 0 || scan.length < secondCaret + 5) {
			return null;
		}
		const expiration = scan.substring(secondCaret + 1, secondCaret + 5);
		const year = Number(expiration.substring(0, 2));
		const month = expiration.substring(2, 4);
		const currentYearShort = Number(String(currentYear).slice(-2));
		return {
			type: 'card',
			fields: {
				cardNumber: formatCardNumber(scan.substring(cardMatch.index + 1, cardMatch.index + 17)),
				expirationMonth: month,
				expirationYear: year - currentYearShort,
			},
		};
	}

	function validateRenewalFields(wtDate, instructor) {
		return {
			missingDate: !wtDate,
			missingInstructor: !instructor,
			valid: Boolean(wtDate && instructor),
		};
	}

	function getPrefillStickerIndex(status, prefillStatuses, optionCount) {
		return prefillStatuses.map(String).includes(String(status)) ? optionCount - 1 : null;
	}

	function isAmountOverrideEditable(checked) {
		return Boolean(checked);
	}

	function getCertificationTestId(certificationName) {
		const name = String(certificationName).toLowerCase();
		if (name.includes('steel')) {
			return 1;
		}
		if (name.includes('holster')) {
			return 2;
		}
		return null;
	}

	function canSearchCertification(testId, email, siteUrl) {
		return Boolean(testId && email && siteUrl && siteUrl.length > 1);
	}

	function getPaymentVisibility(paymentType) {
		return {
			showCardForm: paymentType === 'creditnow',
			showAddButton: paymentType !== 'creditnow',
			showOnlineSearch: paymentType === 'online',
		};
	}

	return {
		calculateCart: calculateCart,
		canSearchCertification: canSearchCertification,
		formatCardNumber: formatCardNumber,
		getCertificationTestId: getCertificationTestId,
		getPaymentVisibility: getPaymentVisibility,
		getPrefillStickerIndex: getPrefillStickerIndex,
		isAmountOverrideEditable: isAmountOverrideEditable,
		normalizeZeroValue: normalizeZeroValue,
		parseSwipe: parseSwipe,
		titleCase: titleCase,
		validateRenewalFields: validateRenewalFields,
	};
});
