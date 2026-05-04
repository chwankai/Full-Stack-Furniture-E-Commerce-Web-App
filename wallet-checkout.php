<?php
require_once __DIR__ . '/includes/customer_session.php';
error_reporting(0);
include 'includes/config.php';
require_once __DIR__ . '/includes/stripe.php';
require_once __DIR__ . '/includes/recaptcha.php';

function stripe_wallet_debug_log($message)
{
	error_log('[stripe-wallet] ' . $message);
}

function wallet_checkout_validate_recaptcha()
{
	if (!recaptcha_is_configured()) {
		throw new Exception('recaptcha_not_configured');
	}

	$token = $_POST['g-recaptcha-response'] ?? '';
	$remote_ip = $_SERVER['REMOTE_ADDR'] ?? '';

	if (!recaptcha_verify_response($token, $remote_ip)) {
		throw new Exception('recaptcha_failed');
	}
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$tvalue = isset($_SESSION['pending_wallet_topup']) ? (float) $_SESSION['pending_wallet_topup'] : 0;
$topup_payment_reference = isset($_SESSION['pending_wallet_topup_ref']) ? $_SESSION['pending_wallet_topup_ref'] : '';
$stripe_state = isset($_GET['stripe']) ? $_GET['stripe'] : '';
$stripe_session_id = isset($_GET['session_id']) ? trim($_GET['session_id']) : '';

if (strlen($_SESSION['login']) == 0) {
	header('location:login.php');
	exit;
}

if ($stripe_state === 'cancel') {
	header('Location: wallet-checkout.php?message=Stripe%20payment%20was%20cancelled.');
	exit;
}

if ($stripe_state === 'success' && $stripe_session_id !== '') {
	try {
		$session = stripe_retrieve_checkout_session($stripe_session_id);
		$metadata = isset($session['metadata']) && is_array($session['metadata']) ? $session['metadata'] : [];
		$session_reference = $metadata['payment_reference'] ?? '';
		$session_type = $metadata['payment_type'] ?? '';
		$session_user_id = isset($metadata['user_id']) ? (int) $metadata['user_id'] : 0;
		$session_amount = isset($metadata['topup_amount']) ? (float) $metadata['topup_amount'] : 0;

		if (
			($session['payment_status'] ?? '') !== 'paid'
			|| $session_type !== 'wallet_topup'
			|| $session_reference === ''
			|| $session_user_id !== (int) $_SESSION['id']
			|| $session_amount < 10
			|| ($tvalue >= 10 && abs($session_amount - $tvalue) > 0.009)
		) {
			throw new Exception('invalid_session');
		}

		$transaction_started = false;

		try {
			if (!mysqli_begin_transaction($con)) {
				throw new Exception('begin_failed');
			}
			$transaction_started = true;

			$existing_topup_query = mysqli_query($con, "SELECT id FROM transaction WHERE user_id='" . $_SESSION['id'] . "' AND action='Reload' AND transaction_ref='" . mysqli_real_escape_string($con, $stripe_session_id) . "' LIMIT 1");
			if ($existing_topup_query && mysqli_fetch_assoc($existing_topup_query)) {
				unset($_SESSION['pending_wallet_topup']);
				unset($_SESSION['pending_wallet_topup_ref']);
				mysqli_rollback($con);
				header('Location: wallet.php?message=Top%20up%20successfully.');
				exit;
			}

			$old_value_query = mysqli_query($con, "SELECT balance FROM users WHERE id='" . $_SESSION['id'] . "' LIMIT 1 FOR UPDATE");
			if (!$old_value_query) {
				throw new Exception('query_failed');
			}

			$old_value_info = mysqli_fetch_assoc($old_value_query);
			if (!$old_value_info) {
				throw new Exception('query_failed');
			}

			$old_value = (float) $old_value_info['balance'];
			$new_value = $old_value + $session_amount;

			$update_balance_query = mysqli_query($con, "UPDATE users SET balance = $new_value WHERE id='" . $_SESSION['id'] . "'");
			if (!$update_balance_query) {
				throw new Exception('update_balance_failed');
			}

			$insert_transaction_query = mysqli_query($con, "INSERT INTO transaction (user_id, action, amount, transaction_ref) VALUES ('" . $_SESSION['id'] . "', 'Reload', '$session_amount', '" . mysqli_real_escape_string($con, $stripe_session_id) . "')");
			if (!$insert_transaction_query) {
				throw new Exception('insert_transaction_failed');
			}

			if (!mysqli_commit($con)) {
				throw new Exception('commit_failed');
			}
			$transaction_started = false;
		} catch (Exception $e) {
			if ($transaction_started) {
				mysqli_rollback($con);
			}
			throw $e;
		}

		unset($_SESSION['pending_wallet_topup']);
		unset($_SESSION['pending_wallet_topup_ref']);
		header('Location: wallet.php?message=Top%20up%20successfully.');
		exit;
	} catch (Exception $e) {
		stripe_wallet_debug_log('Wallet top-up verification failed: ' . $e->getMessage());
		header('Location: wallet-checkout.php?message=Unable%20to%20verify%20your%20Stripe%20payment.%20Please%20contact%20support%20if%20you%20were%20charged.');
		exit;
	}
}

if ($tvalue < 10 || $topup_payment_reference === '') {
	unset($_SESSION['pending_wallet_topup']);
	unset($_SESSION['pending_wallet_topup_ref']);
	header('Location: wallet.php?message=Please%20start%20your%20reload%20again.');
	exit;
}

if (isset($_POST['submit'])) {
	$payment_reference = isset($_POST['payment_reference']) ? trim($_POST['payment_reference']) : '';

	if ($payment_reference === '' || $payment_reference !== $topup_payment_reference) {
		header('Location: wallet-checkout.php?message=This%20top%20up%20request%20is%20no%20longer%20valid.%20Please%20try%20again.');
		exit;
	}

	try {
		wallet_checkout_validate_recaptcha();
	} catch (Exception $e) {
		$message = $e->getMessage() === 'recaptcha_not_configured'
			? 'Google%20reCAPTCHA%20is%20not%20configured.%20Please%20set%20RECAPTCHA_SITE_KEY%20and%20RECAPTCHA_SECRET_KEY%20in%20.env.'
			: 'Please%20complete%20the%20Google%20reCAPTCHA%20challenge%20before%20continuing.';
		header("Location: wallet-checkout.php?message=$message");
		exit;
	}

	if (!stripe_is_configured()) {
		header('Location: wallet-checkout.php?message=Stripe%20is%20not%20configured.%20Please%20add%20your%20test%20secret%20key%20to%20.env.');
		exit;
	}

	try {
		$success_url = stripe_get_base_url() . '/wallet-checkout.php?stripe=success&session_id={CHECKOUT_SESSION_ID}';
		$cancel_url = stripe_get_base_url() . '/wallet-checkout.php?stripe=cancel';
		$session = stripe_create_checkout_session([
			'mode' => 'payment',
			'success_url' => $success_url,
			'cancel_url' => $cancel_url,
			'customer_email' => $_SESSION['login'],
			'client_reference_id' => $payment_reference,
			'metadata' => [
				'payment_type' => 'wallet_topup',
				'payment_reference' => $payment_reference,
				'topup_amount' => number_format($tvalue, 2, '.', ''),
				'user_id' => (string) $_SESSION['id'],
			],
			'line_items' => [[
				'quantity' => 1,
				'price_data' => [
					'currency' => 'myr',
					'unit_amount' => (int) round($tvalue * 100),
					'product_data' => [
						'name' => 'Wallet Top Up',
						'description' => 'OFS e-Wallet reload',
					],
				],
			]],
		]);

		if (empty($session['url'])) {
			throw new Exception('missing_url');
		}

		header('Location: ' . $session['url']);
		exit;
	} catch (Exception $e) {
		stripe_wallet_debug_log('Wallet top-up checkout session creation failed: ' . $e->getMessage());
		header('Location: wallet-checkout.php?message=Unable%20to%20start%20Stripe%20checkout.%20Please%20try%20again.');
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="keywords" content="MediaCenter, Template, eCommerce">
	<meta name="robots" content="all">

	<title>Shopping Portal | Top Up Wallet</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/orange.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.css">
	<link rel="stylesheet" href="assets/css/card_payment.css">
	<link rel="stylesheet" href="assets/css/owl.transitions.css">
	<link href="assets/css/lightbox.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<link rel="stylesheet" href="assets/css/rateit.css">
	<link rel="stylesheet" href="assets/css/config.css">
	<link rel="stylesheet" href="assets/css/loader.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" href="assets/images/favicon.ico">
	<script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
	<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
	<script>
		var message = '<?php echo isset($_GET['message']) ? $_GET['message'] : ''; ?>';
		if (message !== '') {
			alert(message);
		}
	</script>
</head>

<body class="cnt-home">
	<header class="header-style-1">
		<?php include 'includes/top-header.php'; ?>
		<?php include 'includes/main-header.php'; ?>
		<?php include 'includes/menu-bar.php'; ?>
	</header>
	<div class="breadcrumb">
		<div class="container">
			<div class="breadcrumb-inner">
				<ul class="list-inline list-unstyled">
					<li><a href="index.php">Home</a></li>
					<li class="active">Top Up Wallet</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="body-content outer-top-bd">
		<div class="container">
			<div class="checkout-box faq-page inner-bottom-sm">
				<div class="row">
					<div class="col-md-12">
						<h2>TOP UP YOUR WALLET: RM <?php echo number_format($tvalue, 2); ?></h2>
						<div class="panel-group checkout-steps" id="accordion">
							<div class="panel panel-default checkout-step-01">
								<div class="panel-heading">
									<h4 class="unicase-checkout-title">
										<a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
											Complete your top-up with Stripe Checkout
										</a>
									</h4>
								</div>

								<div id="collapseOne" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="card-details">
											<div>
												<img alt="Stripe Checkout" class="cc-logo" title="Stripe Checkout" src="assets/images/payment_icon.svg" height="77">
											</div>
											<br>
											<hr style="border:1px solid #ccc; margin: 5px 30px;">
											<br>
											<form id="payment-form" name="payment" method="post">
												<input type="hidden" name="payment_reference" value="<?php echo htmlentities($topup_payment_reference); ?>">
												<p>Top-up amount</p>
												<div class="order-details">
													<div><b>Wallet Reload:</b> RM<?php echo number_format($tvalue, 2); ?></div>
												</div>
												<br>
												<p>Your card details will be entered securely on Stripe's hosted checkout page.</p>
												<?php if (!stripe_is_configured()) { ?>
													<div class="alert alert-warning">Add `STRIPE_SECRET_KEY` to `.env` before testing wallet top-up.</div>
												<?php } ?>
												<?php if (!recaptcha_is_configured()) { ?>
													<div class="alert alert-warning">Add `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` to `.env` to enable Google reCAPTCHA.</div>
												<?php } ?>
												<div class="g-recaptcha" id="divRecaptcha"></div>
												<div>
													<input type="submit" value="PAY WITH STRIPE" name="submit" id="submitBtn" class="btn btn-upper btn-primary pull-right outer-right-xs">
												</div>
											</form>
											<br><br>
										</div>

										<div id="loaderModal" class="modal fade" data-backdrop="static" data-keyboard="false">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-body">
														<div id="loader"></div>
														<p style="text-align:center;">Please wait while we are processing your request...</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include 'includes/brands-slider.php'; ?>
		</div>
	</div>
	<?php include 'includes/footer.php'; ?>
	<script src="assets/js/jquery-1.11.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/jquery.rateit.min.js"></script>
	<script type="text/javascript" src="assets/js/lightbox.min.js"></script>
	<script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>
	<script src="switchstylesheet/switchstylesheet.js"></script>
	<script>
		const recaptchaSiteKey = <?php echo json_encode(RECAPTCHA_SITE_KEY); ?>;

		$(document).ready(function() {
			$(".changecolor").switchstylesheet({ seperator: "color" });
			$('.show-theme-options').click(function() {
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
			$('.show-theme-options').delay(2000).trigger('click');
		});

		function onloadCallback() {
			if (!recaptchaSiteKey) {
				return;
			}

			grecaptcha.render('divRecaptcha', {
				'sitekey': recaptchaSiteKey
			});
		}

		window.onload = function() {
			document.getElementById('submitBtn').addEventListener('click', function(event) {
				if (recaptchaSiteKey && !document.querySelector('#payment-form textarea[name="g-recaptcha-response"]')?.value) {
					event.preventDefault();
					alert('Please complete the Google reCAPTCHA challenge first.');
					return;
				}
				$('#loaderModal').modal('show');
			});
		};
	</script>
</body>

</html>
