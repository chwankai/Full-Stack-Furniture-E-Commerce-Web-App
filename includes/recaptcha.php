<?php

function recaptcha_is_configured()
{
	return defined('RECAPTCHA_SITE_KEY') && RECAPTCHA_SITE_KEY !== ''
		&& defined('RECAPTCHA_SECRET_KEY') && RECAPTCHA_SECRET_KEY !== '';
}

function recaptcha_verify_response($token, $remote_ip = '')
{
	if (!recaptcha_is_configured()) {
		throw new Exception('recaptcha_not_configured');
	}

	if (!is_string($token) || trim($token) === '') {
		return false;
	}

	$params = [
		'secret' => RECAPTCHA_SECRET_KEY,
		'response' => trim($token),
	];

	if ($remote_ip !== '') {
		$params['remoteip'] = $remote_ip;
	}

	$ch = curl_init();
	curl_setopt_array($ch, [
		CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($params),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 30,
	]);

	$response = curl_exec($ch);
	if ($response === false) {
		$error = curl_error($ch);
		curl_close($ch);
		throw new Exception('recaptcha_curl_error:' . $error);
	}

	curl_close($ch);
	$decoded = json_decode($response, true);

	return is_array($decoded) && !empty($decoded['success']);
}
