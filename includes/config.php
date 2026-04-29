<?php

if (!defined('APP_BOOTSTRAPPED')) {
	define('APP_BOOTSTRAPPED', true);

	$autoload_path = dirname(__DIR__) . '/vendor/autoload.php';
	if (file_exists($autoload_path)) {
		require_once $autoload_path;
	}

	if (class_exists('Dotenv\\Dotenv')) {
		$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
		$dotenv->safeLoad();
	}

	$app_url = $_ENV['APP_URL'] ?? getenv('APP_URL') ?? '';
	$stripe_secret_key = $_ENV['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY') ?? '';
	$stripe_publishable_key = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? getenv('STRIPE_PUBLISHABLE_KEY') ?? '';
	$recaptcha_site_key = $_ENV['RECAPTCHA_SITE_KEY'] ?? getenv('RECAPTCHA_SITE_KEY') ?? '';
	$recaptcha_secret_key = $_ENV['RECAPTCHA_SECRET_KEY'] ?? getenv('RECAPTCHA_SECRET_KEY') ?? '';

	define('APP_URL', trim($app_url));
	define('STRIPE_SECRET_KEY', trim($stripe_secret_key));
	define('STRIPE_PUBLISHABLE_KEY', trim($stripe_publishable_key));
	define('RECAPTCHA_SITE_KEY', trim($recaptcha_site_key));
	define('RECAPTCHA_SECRET_KEY', trim($recaptcha_secret_key));
}

define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shopping');
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

$host = 'localhost';
$dbname = 'shopping';
$username = 'root';
$password = '';
$mysqli = new mysqli(
	hostname: $host,
	username: $username,
	password: $password,
	database: $dbname
);

if (mysqli_connect_errno()) {
	echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

return $mysqli;
?>
