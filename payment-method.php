<?php
require_once __DIR__ . '/includes/customer_session.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'includes/config.php';
require_once __DIR__ . '/includes/stripe.php';
require_once __DIR__ . '/includes/recaptcha.php';

function stripe_payment_debug_log($message)
{
	error_log('[stripe-payment] ' . $message);
}

function payment_method_validate_recaptcha()
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

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
if (strlen($_SESSION['login']) == 0) {
	header('location:login.php');
	exit;
}

date_default_timezone_set('Asia/Kuala_Lumpur');

if ($order_id <= 0) {
	header('location:pending-orders.php');
	exit;
}

$order_status_query = mysqli_query(
	$con,
	"SELECT id, subtotal, shippingCharge, grandtotal, paymentMethod, payment_reference FROM orders WHERE userId='" . $_SESSION['id'] . "' AND id='$order_id' LIMIT 1"
);
$order_status = $order_status_query ? mysqli_fetch_assoc($order_status_query) : null;

if (!$order_status) {
	header('location:pending-orders.php');
	exit;
}

if (!isset($_SESSION['payment_request_refs']) || !is_array($_SESSION['payment_request_refs'])) {
	$_SESSION['payment_request_refs'] = [];
}
if (!isset($_SESSION['payment_request_refs'][$order_id]) || !is_array($_SESSION['payment_request_refs'][$order_id])) {
	$_SESSION['payment_request_refs'][$order_id] = [];
}
if (empty($_SESSION['payment_request_refs'][$order_id]['card'])) {
	$_SESSION['payment_request_refs'][$order_id]['card'] = bin2hex(random_bytes(16));
}
if (empty($_SESSION['payment_request_refs'][$order_id]['wallet'])) {
	$_SESSION['payment_request_refs'][$order_id]['wallet'] = bin2hex(random_bytes(16));
}

$card_payment_reference = $_SESSION['payment_request_refs'][$order_id]['card'];
$wallet_payment_reference = $_SESSION['payment_request_refs'][$order_id]['wallet'];

$stripe_state = isset($_GET['stripe']) ? $_GET['stripe'] : '';
$stripe_session_id = isset($_GET['session_id']) ? trim($_GET['session_id']) : '';

if ($stripe_state === 'cancel') {
	header("Location: payment-method.php?order_id=$order_id&message=Stripe%20payment%20was%20cancelled.");
	exit;
}

if ($stripe_state === 'success' && $stripe_session_id !== '') {
	try {
		$session = stripe_retrieve_checkout_session($stripe_session_id);
		$metadata = isset($session['metadata']) && is_array($session['metadata']) ? $session['metadata'] : [];
		$session_order_id = isset($metadata['order_id']) ? (int) $metadata['order_id'] : 0;
		$session_user_id = isset($metadata['user_id']) ? (int) $metadata['user_id'] : 0;
		$session_reference = $metadata['payment_reference'] ?? '';
		$session_type = $metadata['payment_type'] ?? '';

		if (
			($session['payment_status'] ?? '') !== 'paid'
			|| $session_type !== 'order_checkout'
			|| $session_order_id !== $order_id
			|| $session_user_id !== (int) $_SESSION['id']
			|| $session_reference === ''
		) {
			throw new Exception('invalid_session');
		}

		$transaction_started = false;

		try {
			if (!mysqli_begin_transaction($con)) {
				throw new Exception('begin_failed');
			}
			$transaction_started = true;

			$pending_order_query = mysqli_query(
				$con,
				"SELECT id FROM orders WHERE userId='" . $_SESSION['id'] . "' AND id='$order_id' AND paymentMethod IS NULL LIMIT 1 FOR UPDATE"
			);
			if (!$pending_order_query) {
				throw new Exception('query_failed');
			}

			$pending_order = mysqli_fetch_assoc($pending_order_query);
			if (!$pending_order) {
				$processed_order_query = mysqli_query(
					$con,
					"SELECT paymentMethod, payment_reference FROM orders WHERE userId='" . $_SESSION['id'] . "' AND id='$order_id' LIMIT 1"
				);
				$processed_order = $processed_order_query ? mysqli_fetch_assoc($processed_order_query) : null;

				if ($processed_order && $processed_order['paymentMethod'] === 'Stripe Checkout' && $processed_order['payment_reference'] === $stripe_session_id) {
					unset($_SESSION['payment_request_refs'][$order_id]);
					if (empty($_SESSION['payment_request_refs'])) {
						unset($_SESSION['payment_request_refs']);
					}
					header('Location: order-history.php?message=Order%20placed%20successfully.');
					exit;
				}

				throw new Exception('already_processed');
			}

			$date = date('Y-m-d H:i:s');
			$stripe_reference = mysqli_real_escape_string($con, $stripe_session_id);
			$update_order_query = mysqli_query(
				$con,
				"UPDATE orders SET orderDate='$date', paymentMethod='Stripe Checkout', payment_reference='$stripe_reference', orderStatus='Order Placed' WHERE userId='" . $_SESSION['id'] . "' AND id='$order_id' AND paymentMethod IS NULL"
			);
			if (!$update_order_query) {
				throw new Exception('update_order_failed');
			}
			if (mysqli_affected_rows($con) <= 0) {
				throw new Exception('already_processed');
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

		unset($_SESSION['payment_request_refs'][$order_id]);
		if (empty($_SESSION['payment_request_refs'])) {
			unset($_SESSION['payment_request_refs']);
		}

		include 'send_order_placed.php';
		header('Location: order-history.php?message=Order%20placed%20successfully.');
		exit;
	} catch (Exception $e) {
		stripe_payment_debug_log('Order verification failed for order ' . $order_id . ': ' . $e->getMessage());
		if ($e->getMessage() === 'already_processed') {
			header('Location: order-history.php?message=This%20order%20has%20already%20been%20processed.');
		} else {
			header("Location: payment-method.php?order_id=$order_id&message=Unable%20to%20verify%20your%20Stripe%20payment.%20Please%20contact%20support%20if%20you%20were%20charged.");
		}
		exit;
	}
}

if (!empty($order_status['paymentMethod'])) {
	header('Location: order-history.php?message=This%20order%20has%20already%20been%20processed.');
	exit;
}

if (isset($_POST['submit'])) {
	$payment_reference = isset($_POST['payment_reference']) ? trim($_POST['payment_reference']) : '';

	if ($payment_reference === '' || $payment_reference !== $card_payment_reference) {
		header("Location: payment-method.php?order_id=$order_id&message=This%20payment%20request%20is%20no%20longer%20valid.%20Please%20try%20again.");
		exit;
	}

	try {
		payment_method_validate_recaptcha();
	} catch (Exception $e) {
		$message = $e->getMessage() === 'recaptcha_not_configured'
			? 'Google%20reCAPTCHA%20is%20not%20configured.%20Please%20set%20RECAPTCHA_SITE_KEY%20and%20RECAPTCHA_SECRET_KEY%20in%20.env.'
			: 'Please%20complete%20the%20Google%20reCAPTCHA%20challenge%20before%20continuing.';
		header("Location: payment-method.php?order_id=$order_id&message=$message");
		exit;
	}

	if (!stripe_is_configured()) {
		header("Location: payment-method.php?order_id=$order_id&message=Stripe%20is%20not%20configured.%20Please%20add%20your%20test%20secret%20key%20to%20.env.");
		exit;
	}

	$pending_order_query = mysqli_query(
		$con,
		"SELECT grandtotal FROM orders WHERE userId='" . $_SESSION['id'] . "' AND id='$order_id' AND paymentMethod IS NULL LIMIT 1"
	);
	$pending_order = $pending_order_query ? mysqli_fetch_assoc($pending_order_query) : null;

	if (!$pending_order) {
		header('Location: order-history.php?message=This%20order%20has%20already%20been%20processed.');
		exit;
	}

	$amount_cents = (int) round(((float) $pending_order['grandtotal']) * 100);

	try {
		$success_url = stripe_get_base_url() . '/payment-method.php?order_id=' . $order_id . '&stripe=success&session_id={CHECKOUT_SESSION_ID}';
		$cancel_url = stripe_get_base_url() . '/payment-method.php?order_id=' . $order_id . '&stripe=cancel';
		$session = stripe_create_checkout_session([
			'mode' => 'payment',
			'success_url' => $success_url,
			'cancel_url' => $cancel_url,
			'customer_email' => $_SESSION['login'],
			'client_reference_id' => $payment_reference,
			'metadata' => [
				'payment_type' => 'order_checkout',
				'payment_reference' => $payment_reference,
				'order_id' => (string) $order_id,
				'user_id' => (string) $_SESSION['id'],
			],
			'line_items' => [[
				'quantity' => 1,
				'price_data' => [
					'currency' => 'myr',
					'unit_amount' => $amount_cents,
					'product_data' => [
						'name' => 'Order #' . $order_id,
						'description' => 'Furniture order checkout',
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
		stripe_payment_debug_log('Order checkout session creation failed for order ' . $order_id . ': ' . $e->getMessage());
		header("Location: payment-method.php?order_id=$order_id&message=Unable%20to%20start%20Stripe%20checkout.%20Please%20try%20again.");
		exit;
	}
} elseif (isset($_POST['e-submit'])) {
	$payment_reference = isset($_POST['payment_reference']) ? trim($_POST['payment_reference']) : '';
	$transaction_started = false;

	if ($payment_reference === '' || $payment_reference !== $wallet_payment_reference) {
		header("Location: payment-method.php?order_id=$order_id&message=This%20payment%20request%20is%20no%20longer%20valid.%20Please%20try%20again.");
		exit;
	}

	try {
		payment_method_validate_recaptcha();
	} catch (Exception $e) {
		$message = $e->getMessage() === 'recaptcha_not_configured'
			? 'Google%20reCAPTCHA%20is%20not%20configured.%20Please%20set%20RECAPTCHA_SITE_KEY%20and%20RECAPTCHA_SECRET_KEY%20in%20.env.'
			: 'Please%20complete%20the%20Google%20reCAPTCHA%20challenge%20before%20continuing.';
		header("Location: payment-method.php?order_id=$order_id&message=$message");
		exit;
	}

	try {
		if (!mysqli_begin_transaction($con)) {
			throw new Exception('begin_failed');
		}
		$transaction_started = true;

		$pending_order_query = mysqli_query(
			$con,
			"SELECT grandtotal FROM orders WHERE userId='" . $_SESSION['id'] . "' AND id = '$order_id' AND paymentMethod IS NULL LIMIT 1 FOR UPDATE"
		);
		if (!$pending_order_query) {
			throw new Exception('query_failed');
		}

		$pending_order = mysqli_fetch_assoc($pending_order_query);
		if (!$pending_order) {
			$processed_order_query = mysqli_query(
				$con,
				"SELECT paymentMethod, payment_reference FROM orders WHERE userId='" . $_SESSION['id'] . "' AND id='$order_id' LIMIT 1"
			);
			$processed_order = $processed_order_query ? mysqli_fetch_assoc($processed_order_query) : null;

			if ($processed_order && $processed_order['paymentMethod'] === 'e-Wallet' && $processed_order['payment_reference'] === $payment_reference) {
				unset($_SESSION['payment_request_refs'][$order_id]);
				if (empty($_SESSION['payment_request_refs'])) {
					unset($_SESSION['payment_request_refs']);
				}
				header('Location: order-history.php?message=Order%20placed%20successfully.');
				exit;
			}

			throw new Exception('already_processed');
		}

		$query = mysqli_query($con, "SELECT users.balance as balance FROM users WHERE id = '" . mysqli_real_escape_string($con, $_SESSION['id']) . "' LIMIT 1 FOR UPDATE");
		if (!$query) {
			throw new Exception('query_failed');
		}

		$row = mysqli_fetch_array($query);
		if (!$row) {
			throw new Exception('query_failed');
		}

		$balance = (float) $row['balance'];
		$grandtotal = (float) $pending_order['grandtotal'];

		if ($balance < $grandtotal) {
			throw new Exception('insufficient_balance');
		}

		$new_balance = $balance - $grandtotal;
		$date = date('Y-m-d H:i:s');

		$update_balance_query = mysqli_query($con, "UPDATE users SET balance='$new_balance' WHERE id='" . $_SESSION['id'] . "'");
		if (!$update_balance_query) {
			throw new Exception('update_balance_failed');
		}

		$update_order_query = mysqli_query($con, "UPDATE orders SET orderDate='$date', paymentMethod='e-Wallet', payment_reference='$payment_reference', orderStatus='Order Placed' WHERE userId='" . $_SESSION['id'] . "' AND id = '$order_id' AND paymentMethod IS NULL");
		if (!$update_order_query) {
			throw new Exception('update_order_failed');
		}
		if (mysqli_affected_rows($con) <= 0) {
			throw new Exception('already_processed');
		}

		$insert_transaction_query = mysqli_query($con, "INSERT INTO transaction (user_id, order_id, action, amount, transaction_ref) VALUES ('" . $_SESSION['id'] . "','$order_id', 'Pay', '$grandtotal', '$payment_reference')");
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

		if ($e->getMessage() === 'insufficient_balance') {
			header('Location: wallet.php?message=Balance%20insufficient.%20Please%20top%20up%20and%20try%20again.');
		} elseif ($e->getMessage() === 'already_processed') {
			header('Location: order-history.php?message=This%20order%20has%20already%20been%20processed.');
		} else {
			header("Location: payment-method.php?order_id=$order_id&message=Unable%20to%20process%20your%20wallet%20payment.%20Please%20try%20again.");
		}
		exit;
	}

	unset($_SESSION['payment_request_refs'][$order_id]);
	if (empty($_SESSION['payment_request_refs'])) {
		unset($_SESSION['payment_request_refs']);
	}

	include 'send_order_placed.php';
	header('Location: order-history.php?message=Order%20placed%20successfully.');
	exit;
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

	<title>Shopping Portal | Checkout</title>
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
					<li class="active">Checkout</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="body-content outer-top-bd">
		<div class="container">
			<div class="checkout-box faq-page inner-bottom-sm">
				<div class="row">
					<div class="col-md-12">
						<h2>CHECKOUT</h2>
						<div class="panel-group checkout-steps" id="accordion">
							<div class="panel panel-default checkout-step-01">
								<div class="panel-heading">
									<h4 class="unicase-checkout-title">
										<a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
											Choose your payment method
										</a>
									</h4>
								</div>

								<div id="collapseOne" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="methods" style="display:flex;">
											<div onclick="showCardPayment()" id="tColorA" class="methods_button"><i class="fa-solid fa-credit-card"></i>Pay by Card</div>
											<div onclick="showWalletPayment()" id="tColorB" class="methods_button"><i class="fa-solid fa-wallet"></i>OFS e-Wallet</div>
										</div>

										<div class="card-details">
											<div>
												<img alt="Stripe Checkout" class="cc-logo" title="Stripe Checkout" src="assets/images/payment_icon.svg"  height="77">
											</div>
											<br>
											<hr style="border:1px solid #ccc; margin: 5px 0px;">
											<div class="order-details">
												<div><b>Order ID:</b> #<?php echo $order_id; ?></div>
												<div><b>Subtotal:</b> RM<?php echo number_format((float) $order_status['subtotal'], 2); ?></div>
												<div><b>Shipping Charge:</b> RM<?php echo number_format((float) $order_status['shippingCharge'], 2); ?></div>
												<div><b>Grandtotal:</b> RM<?php echo number_format((float) $order_status['grandtotal'], 2); ?></div>
											</div>
											<hr style="border:1px solid #ccc; margin: 5px 0px;">

											<form method="post" id="stripeCheckoutForm">
												<input type="hidden" name="payment_reference" value="<?php echo htmlentities($card_payment_reference); ?>">
												<p style="text-align: center;">You will be redirected to Stripe to complete the payment securely.</p>
												<?php if (!stripe_is_configured()) { ?>
													<div class="alert alert-warning">Add `STRIPE_SECRET_KEY` to `.env` before testing card checkout.</div>
												<?php } ?>
												<?php if (!recaptcha_is_configured()) { ?>
													<div class="alert alert-warning">Add `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` to `.env` to enable Google reCAPTCHA.</div>
												<?php } ?>
												<div class="g-recaptcha" id="divRecaptcha1" style="display: flex;justify-content:center;"></div>
												<div>
													<input type="submit" value="PAY WITH STRIPE" name="submit" id="submitBtn" style="text-align:center !important; margin:40px auto 0px;display:block;" class="btn btn-upper btn-primary outer-right-xs">
												</div>
											</form>
											<br><br>
										</div>

										<div class="ewallet-details" style="display:none;">
											<div>
												<img alt="Wallet" class="cc-logo" title="Wallet" src="assets/images/ewallet_icon.png" width="auto" height="77" style="width:13% !important;">
											</div>
											<br>
											<hr style="border:1px solid #ccc; margin: 5px 0px;">
											<div class="order-details">
												<div><b>Order ID:</b> #<?php echo $order_id; ?></div>
												<div><b>Subtotal:</b> RM<?php echo number_format((float) $order_status['subtotal'], 2); ?></div>
												<div><b>Shipping Charge:</b> RM<?php echo number_format((float) $order_status['shippingCharge'], 2); ?></div>
												<div><b>Grandtotal:</b> RM<?php echo number_format((float) $order_status['grandtotal'], 2); ?></div>
											</div>
											<hr style="border:1px solid #ccc; margin: 5px 0px;">
											<form method="post" id="walletCheckoutForm">
												<input type="hidden" name="payment_reference" value="<?php echo htmlentities($wallet_payment_reference); ?>">
												<?php
												$tvalue = mysqli_query($con, "SELECT users.balance FROM users WHERE id='" . $_SESSION['id'] . "'");
												$tvalue_info = mysqli_fetch_array($tvalue);
												?>
												<div class="wallet-balance"><b>Wallet Balance:</b> RM <?php echo number_format((float) ($tvalue_info['balance'] ?? 0), 2); ?></div>
												<br>
												<?php if (!recaptcha_is_configured()) { ?>
													<div class="alert alert-warning">Add `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` to `.env` to enable Google reCAPTCHA.</div>
												<?php } ?>
												<div class="g-recaptcha" style="margin: 0 auto; width: 304px;" id="divRecaptcha2"></div>
												<div>
													<input type="submit" value="PAY WITH YOUR WALLET" name="e-submit" id="e-submit" style="text-align:center !important; margin:40px auto 0px;display:block;" class="btn btn-upper btn-primary outer-right-xs">
												</div>
											</form>
											<br><br>
										</div>

										<div id="loaderModal" class="modal fade" data-backdrop="static" data-keyboard="false">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-body">
														<div id="loader"></div>
														<p style="text-align:center;">Please wait while we are processing your order...</p>
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

		function showCardPayment() {
			document.querySelector('.card-details').style.display = 'block';
			document.querySelector('.ewallet-details').style.display = 'none';
		}

		function showWalletPayment() {
			document.querySelector('.card-details').style.display = 'none';
			document.querySelector('.ewallet-details').style.display = 'block';
		}

		function onloadCallback() {
			if (!recaptchaSiteKey) {
				return;
			}

			grecaptcha.render('divRecaptcha1', { 'sitekey': recaptchaSiteKey });
			grecaptcha.render('divRecaptcha2', { 'sitekey': recaptchaSiteKey });
		}

		window.onload = function() {
			document.getElementById('submitBtn').addEventListener('click', function(event) {
				if (recaptchaSiteKey && !document.querySelector('#stripeCheckoutForm textarea[name="g-recaptcha-response"]')?.value) {
					event.preventDefault();
					alert('Please complete the Google reCAPTCHA challenge first.');
					return;
				}
				$('#loaderModal').modal('show');
			});

			document.getElementById('e-submit').addEventListener('click', function(event) {
				if (recaptchaSiteKey && !document.querySelector('#walletCheckoutForm textarea[name="g-recaptcha-response"]')?.value) {
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
