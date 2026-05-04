<?php

function stripe_is_configured()
{
	return defined('STRIPE_SECRET_KEY') && STRIPE_SECRET_KEY !== '';
}

function stripe_get_base_url()
{
	if (defined('APP_URL') && APP_URL !== '') {
		return rtrim(APP_URL, '/');
	}

	$is_https = (
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
	);
	$scheme = $is_https ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

	return $scheme . '://' . $host;
}

function stripe_flatten_params(array $params, $prefix = null)
{
	$flattened = [];

	foreach ($params as $key => $value) {
		$composed_key = $prefix === null ? $key : $prefix . '[' . $key . ']';

		if (is_array($value)) {
			$flattened = array_merge($flattened, stripe_flatten_params($value, $composed_key));
			continue;
		}

		$flattened[$composed_key] = $value;
	}

	return $flattened;
}

function stripe_api_request($method, $endpoint, array $params = [])
{
	if (!stripe_is_configured()) {
		throw new Exception('stripe_not_configured');
	}

	$ch = curl_init();
	$url = 'https://api.stripe.com/v1/' . ltrim($endpoint, '/');
	$headers = [
		'Authorization: Bearer ' . STRIPE_SECRET_KEY,
	];

	if (strtoupper($method) === 'GET' && !empty($params)) {
		$url .= '?' . http_build_query(stripe_flatten_params($params));
	}

	curl_setopt_array($ch, [
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CUSTOMREQUEST => strtoupper($method),
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_TIMEOUT => 30,
	]);

	if (strtoupper($method) !== 'GET') {
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(stripe_flatten_params($params)));
	}

	$response = curl_exec($ch);
	if ($response === false) {
		$error = curl_error($ch);
		curl_close($ch);
		throw new Exception('stripe_curl_error:' . $error);
	}

	$status_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$decoded = json_decode($response, true);
	if ($status_code >= 400) {
		$message = $decoded['error']['message'] ?? 'Stripe request failed.';
		throw new Exception('stripe_api_error:' . $message);
	}

	if (!is_array($decoded)) {
		throw new Exception('stripe_invalid_response');
	}

	return $decoded;
}

function stripe_create_checkout_session(array $params)
{
	return stripe_api_request('POST', 'checkout/sessions', $params);
}

function stripe_retrieve_checkout_session($session_id)
{
	return stripe_api_request('GET', 'checkout/sessions/' . rawurlencode($session_id), [
		'expand' => ['payment_intent'],
	]);
}
