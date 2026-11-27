<?php

namespace backend\components;

use yii\base\InvalidConfigException;

class GoogleAddressApi
{
	private const ADDRESS_VALIDATION_URL = 'https://addressvalidation.googleapis.com/v1:validateAddress';
	private const GEOCODING_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

	private $apiKey;

	public function __construct($apiKey)
	{
		$this->apiKey = is_string($apiKey) ? trim($apiKey) : '';
		if ($this->apiKey === '') {
			throw new InvalidConfigException('Set params.googleMapsApiKey in common/config/main-local.php.');
		}
	}

	public function validateAddress(array $address)
	{
		$street = trim((string)($address['street'] ?? ''));
		if ($street === '') {
			throw new \InvalidArgumentException('Enter a street address to validate.');
		}
		$zip = trim((string)($address['zip'] ?? ''));
		if (!preg_match('/^\d{5}(?:-\d{4})?$/', $zip)) {
			throw new \InvalidArgumentException('Enter a valid US ZIP code to validate the address.');
		}

		$postalAddress = [
			'regionCode' => 'US',
			'addressLines' => [$street],
			'postalCode' => $zip,
		];
		foreach ([
			'city' => 'locality',
			'state' => 'administrativeArea',
		] as $input => $googleField) {
			$value = trim((string)($address[$input] ?? ''));
			if ($value !== '') {
				$postalAddress[$googleField] = $value;
			}
		}

		if (strlen(implode(' ', array_filter([
			$street,
			$postalAddress['locality'] ?? '',
			$postalAddress['administrativeArea'] ?? '',
			$postalAddress['postalCode'] ?? '',
		]))) > 280) {
			throw new \InvalidArgumentException('The address must not exceed 280 characters.');
		}

		return $this->request(self::ADDRESS_VALIDATION_URL, [
			'address' => $postalAddress,
		], true);
	}

	public function getCityStateByZip($zip)
	{
		$zip = trim((string)$zip);
		if (!preg_match('/^\d{5}(?:-\d{4})?$/', $zip)) {
			throw new \InvalidArgumentException('Enter a valid US ZIP code.');
		}

		$response = $this->request(self::GEOCODING_URL, [
			'components' => 'postal_code:' . $zip . '|country:US',
		]);
		if (($response['status'] ?? null) !== 'OK') {
			$status = (string)($response['status'] ?? 'unknown status');
			$message = trim((string)($response['error_message'] ?? ''));
			throw new \RuntimeException('Google Geocoding API returned ' . $status . ($message === '' ? '.' : ': ' . $message));
		}
		if (empty($response['results'][0]['address_components'])) {
			throw new \RuntimeException('Google could not find a city and state for that ZIP code.');
		}

		$components = $response['results'][0]['address_components'];
		$city = '';
		$state = '';
		foreach ($components as $component) {
			$types = $component['types'] ?? [];
			if ($city === '' && array_intersect(['locality', 'postal_town'], $types)) {
				$city = (string)($component['long_name'] ?? '');
			}
			if ($state === '' && in_array('administrative_area_level_1', $types, true)) {
				$state = (string)($component['short_name'] ?? '');
			}
		}

		if ($city === '' || $state === '') {
			throw new \RuntimeException('Google did not return a city and state for that ZIP code.');
		}

		return ['City' => $city, 'State' => $state];
	}

	private function request($url, array $parameters, $post = false)
	{
		if (!function_exists('curl_init')) {
			throw new \RuntimeException('The PHP cURL extension is required for Google Maps API requests.');
		}

		$handle = curl_init($url . '?' . http_build_query(['key' => $this->apiKey] + ($post ? [] : $parameters)));
		if ($handle === false) {
			throw new \RuntimeException('Could not initialize the Google Maps API request.');
		}

		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_HTTPHEADER => ['Accept: application/json'],
		];
		if (PHP_OS_FAMILY === 'Windows' && defined('CURLSSLOPT_NATIVE_CA')) {
			$options[CURLOPT_SSL_OPTIONS] = CURLSSLOPT_NATIVE_CA;
		}
		if ($post) {
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = json_encode($parameters, JSON_THROW_ON_ERROR);
			$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
		}
		curl_setopt_array($handle, $options);
		$body = curl_exec($handle);
		$error = curl_error($handle);
		$status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close($handle);

		if ($body === false) {
			throw new \RuntimeException('Google Maps API request failed: ' . $error);
		}
		try {
			$data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $exception) {
			throw new \RuntimeException('Google Maps API returned invalid JSON.', 0, $exception);
		}
		if ($status < 200 || $status >= 300) {
			$message = $data['error']['message'] ?? 'Google Maps API returned HTTP ' . $status . '.';
			throw new \RuntimeException((string)$message);
		}

		return $data;
	}
}
