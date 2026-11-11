'use strict';

const assert = require('node:assert/strict');
const test = require('node:test');
const logic = require('../../web/js/badge-form-logic.js');

test('normalizes a zero field value but preserves other values', function () {
	assert.equal(logic.normalizeZeroValue('0'), '');
	assert.equal(logic.normalizeZeroValue('00'), '00');
	assert.equal(logic.normalizeZeroValue('12'), '12');
});

test('calculates cart line amounts and tax with currency rounding', function () {
	const result = logic.calculateCart([
		{ item: 'Target', sku: 'A1', ea: '10.005', qty: '2', taxRate: '0.06' },
		{ item: 'Skipped', sku: 'B2', ea: '3.50', qty: '0', taxRate: '0.06' },
	], '50', [], false);

	assert.deepEqual(result.cart, [
		{ item: 'Target', sku: 'A1', ea: '10.005', qty: '2', price: '21.21' },
	]);
	assert.deepEqual(result.prices, ['21.21', null]);
	assert.equal(result.itemTotal, 20.01);
	assert.equal(result.taxTotal, 1.20);
	assert.equal(result.amountDue, 71.21);
});

test('sums numeric selected discounts and clamps negative create totals to cash', function () {
	const result = logic.calculateCart([], 20, ['', 's:12.50', 'x', 'd:10'], false);

	assert.equal(result.discount, 22.5);
	assert.equal(result.amountDue, 0);
	assert.equal(result.setCash, true);
});

test('preserves create payment type when total is exactly zero', function () {
	const result = logic.calculateCart([], 20, ['s:20'], false);

	assert.equal(result.amountDue, 0);
	assert.equal(result.setCash, false);
});

test('update calculation changes payment to cash for a zero total', function () {
	const result = logic.calculateCart([], 20, ['s:20'], true);

	assert.equal(result.amountDue, 0);
	assert.equal(result.setCash, true);
});

test('formats card numbers as four-digit groups', function () {
	assert.equal(logic.formatCardNumber('4111-2222-3333-4444'), '4111-2222-3333-4444');
	assert.equal(logic.formatCardNumber('41112222333344445555'), '4111-2222-3333-4444');
	assert.equal(logic.formatCardNumber('12ab34'), '1234');
});

test('parses AAMVA driver license names, address and birth year', function () {
	const scan = 'ANSI 6360DACJANEArrowDownDADQArrowDownDCSDOEArrowDown'
		+ 'DBB19951231ArrowDownDAG123 MAIN STArrowDown'
		+ 'DAIBALTIMOREArrowDownDAJMDArrowDownDAK212341234ArrowDown';
	const result = logic.parseSwipe(scan, 2026);

	assert.deepEqual(result, {
		type: 'license',
		fields: {
			firstName: 'Jane Q',
			lastName: 'Doe',
			yob: '1995',
			address: '123 Main St',
			city: 'Baltimore',
			state: 'MD',
			zip: '21234',
		},
	});
});

test('parses alternate AAMVA full-name field', function () {
	const result = logic.parseSwipe('AAMVA6360DAADOE,JANE,QArrowDown', 2026);

	assert.deepEqual(result, {
		type: 'license',
		fields: {
			firstName: 'Jane Q',
			lastName: 'Doe',
		},
	});
});

test('supports MMDDYYYY birth dates when the leading four digits are not a year', function () {
	const result = logic.parseSwipe('ANSI 6360DACJANEArrowDownDADQArrowDown'
		+ 'DCSDOEArrowDownDBB12311995ArrowDown', 2026);

	assert.equal(result.fields.yob, '1995');
});

test('parses a credit-card track and computes expiration year offset', function () {
	const result = logic.parseSwipe('B4111222233334444^DOE/JOHN^2812', 2026);

	assert.deepEqual(result, {
		type: 'card',
		fields: {
			cardNumber: '4111-2222-3333-4444',
			expirationMonth: '12',
			expirationYear: 2,
		},
	});
});

test('rejects unknown and incomplete swipe data', function () {
	assert.equal(logic.parseSwipe('not a supported swipe', 2026), null);
	assert.equal(logic.parseSwipe('ANSI 6360DACJANEArrowDown', 2026), null);
	assert.equal(logic.parseSwipe('B4111222233334444', 2026), null);
	assert.equal(logic.parseSwipe(null, 2026), null);
});

test('validates required renewal training fields', function () {
	assert.deepEqual(logic.validateRenewalFields('', ''), {
		missingDate: true,
		missingInstructor: true,
		valid: false,
	});
	assert.deepEqual(logic.validateRenewalFields('2026-10-01', 'Instructor'), {
		missingDate: false,
		missingInstructor: false,
		valid: true,
	});
});

test('selects the final sticker option for prefill statuses', function () {
	assert.equal(logic.getPrefillStickerIndex(4, [2, '4'], 6), 5);
	assert.equal(logic.getPrefillStickerIndex(1, [2, '4'], 6), null);
	assert.equal(logic.getPrefillStickerIndex(4, [4], 0), -1);
});

test('only enables amount editing when the override is checked', function () {
	assert.equal(logic.isAmountOverrideEditable(true), true);
	assert.equal(logic.isAmountOverrideEditable(false), false);
});

test('title-cases scanned names and addresses', function () {
	assert.equal(logic.titleCase('JANE DOE'), 'Jane Doe');
	assert.equal(logic.titleCase('123 MAIN ST'), '123 Main St');
});

test('maps certification categories to test lookups and requires a valid search context', function () {
	assert.equal(logic.getCertificationTestId('Advanced Steel Challenge'), 1);
	assert.equal(logic.getCertificationTestId('Holster Qualification'), 2);
	assert.equal(logic.getCertificationTestId('Basic Safety'), null);
	assert.equal(logic.canSearchCertification(1, 'user@example.com', 'https://example.test'), true);
	assert.equal(logic.canSearchCertification(1, '', 'https://example.test'), false);
});

test('maps payment types to certification form visibility', function () {
	assert.deepEqual(logic.getPaymentVisibility('creditnow'), {
		showCardForm: true,
		showAddButton: false,
		showOnlineSearch: false,
	});
	assert.deepEqual(logic.getPaymentVisibility('online'), {
		showCardForm: false,
		showAddButton: true,
		showOnlineSearch: true,
	});
	assert.deepEqual(logic.getPaymentVisibility('cash'), {
		showCardForm: false,
		showAddButton: true,
		showOnlineSearch: false,
	});
});
