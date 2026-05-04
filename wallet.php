<?php
require_once __DIR__ . '/includes/customer_session.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//error_reporting(0);
include ('includes/config.php');//checking database connection
if (strlen($_SESSION['login']) == 0) {
    header('location:login.php');
} else {
	if (empty($_SESSION['wallet_withdraw_request_ref'])) {
		$_SESSION['wallet_withdraw_request_ref'] = bin2hex(random_bytes(16));
	}
	$wallet_withdraw_request_ref = $_SESSION['wallet_withdraw_request_ref'];

	if (isset($_POST['topupsubmit']))//update user info and profile
	{
		$value = isset($_POST['topup_value']) ? (float) $_POST['topup_value'] : 0;
		if ($value < 10) {
			header('Location: wallet.php?message=Minimum%20reload%20amount%20is%20RM10.');
			exit;
		}

		$_SESSION['pending_wallet_topup'] = $value;
		$_SESSION['pending_wallet_topup_ref'] = bin2hex(random_bytes(16));
		header('location:wallet-checkout.php');
		exit;
	}
	    else if (isset($_POST['withdrawsubmit'])) {
	        
	        $value = isset($_POST['wtdw_value']) ? (float) $_POST['wtdw_value'] : 0;
	        $withdraw_reference = isset($_POST['withdraw_reference']) ? trim($_POST['withdraw_reference']) : '';
	        $transaction_started = false;

	        try {
	        	if ($withdraw_reference === '' || $withdraw_reference !== $wallet_withdraw_request_ref) {
	        		$existing_withdraw_query = mysqli_query($con, "SELECT id FROM transaction WHERE user_id='" . $_SESSION['id'] . "' AND action='Withdraw' AND transaction_ref='" . mysqli_real_escape_string($con, $withdraw_reference) . "' LIMIT 1");
	        		if ($existing_withdraw_query && mysqli_fetch_assoc($existing_withdraw_query)) {
	        			unset($_SESSION['wallet_withdraw_request_ref']);
	        			header("Location: wallet.php?message=Withdraw%20successfully!%20Please%20wait%20three%20to%20five%20days%20for%20processing.");
	        			exit;
	        		}
	        		throw new Exception('invalid_reference');
	        	}

	        	if ($value <= 0) {
	        		throw new Exception('invalid_amount');
	        	}

	        	if (!mysqli_begin_transaction($con)) {
	        		throw new Exception('begin_failed');
	        	}
	        	$transaction_started = true;

	        	$old_value_query = mysqli_query($con, "SELECT balance FROM users WHERE id ='" . $_SESSION['id'] . "' LIMIT 1 FOR UPDATE");
	        	if (!$old_value_query) {
	        		throw new Exception('query_failed');
	        	}

	        	$old_value_info = mysqli_fetch_assoc($old_value_query);
	        	if (!$old_value_info) {
	        		throw new Exception('query_failed');
	        	}

	        	$old_value = (float) $old_value_info['balance'];
	        	if ($old_value < $value) {
	        		throw new Exception('insufficient_balance');
	        	}

	        	$new_value = $old_value - $value;

			$update_balance_query = mysqli_query($con, "UPDATE users SET balance = $new_value WHERE id='" . $_SESSION['id'] . "'");
			if (!$update_balance_query) {
				throw new Exception('update_balance_failed');
			}

			$insert_transaction_query = mysqli_query($con, "INSERT INTO transaction (user_id, action, amount, transaction_ref)  VALUES ('" . $_SESSION['id'] . "', 'Withdraw', '$value', '$withdraw_reference')");
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
	        		header("Location: wallet.php?message=Balance%20insufficient.%20Please%20try%20again.");
	        	} elseif ($e->getMessage() === 'invalid_reference') {
	        		header("Location: wallet.php?message=This%20withdrawal%20request%20is%20no%20longer%20valid.%20Please%20try%20again.");
	        	} elseif ($e->getMessage() === 'invalid_amount') {
	        		header("Location: wallet.php?message=Please%20enter%20a%20valid%20withdraw%20amount.");
	        	} else {
	        		header("Location: wallet.php?message=Unable%20to%20process%20your%20withdrawal.%20Please%20try%20again.");
	        	}
	        	exit;
	        }

	        unset($_SESSION['wallet_withdraw_request_ref']);
	        header("Location: wallet.php?message=Withdraw%20successfully!%20Please%20wait%20three%20to%20five%20days%20for%20processing.");
	        exit;
	    }    

    date_default_timezone_set('Asia/Kuala_Lumpur');// change according timezone
    $currentTime = date('d-m-Y h:i:s A', time());
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Meta -->
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keywords" content="MediaCenter, Template, eCommerce">
        <meta name="robots" content="all">

        <title>e-Wallet</title>

        <!-- Bootstrap Core CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">

        <!-- Customizable CSS -->
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/orange.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.css">
        <link rel="stylesheet" href="assets/css/owl.transitions.css">
        <link href="assets/css/lightbox.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/animate.min.css">
        <link rel="stylesheet" href="assets/css/rateit.css">
        <link rel="stylesheet" href="assets/css/wallet.css">
        <link rel="stylesheet" href="assets/css/config.css">

        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <script language="javascript" type="text/javascript">
        var popUpWin=0;
            function popUpWindow(URLStr, left, top, width, height)
            {
            if(popUpWin)
            {
            if(!popUpWin.closed) popUpWin.close();
            }
            popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
            }
            
        </script>
        <script>
			// Retrieve the message from the URL parameter
			var message = '<?php echo isset($_GET["message"]) ? $_GET["message"] : ""; ?>';

			// Display the message in an alert
			if (message !== "") {
				alert(message);
			}
		</script>

    </head>

    <body class="cnt-home">
        <header class="header-style-1">

            <!-- ============================================== TOP MENU ============================================== -->
            <?php include ('includes/top-header.php'); ?>
            <!-- ============================================== TOP MENU : END ============================================== -->
            <?php include ('includes/main-header.php'); ?>
            <!-- ============================================== NAVBAR ============================================== -->
            <?php include ('includes/menu-bar.php'); ?>
            <!-- ============================================== NAVBAR : END ============================================== -->

        </header>
        <!-- ============================================== HEADER : END ============================================== -->
        <div class="breadcrumb">
            <div class="container">
                <div class="breadcrumb-inner">
                    <ul class="list-inline list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li class='active'>OFS e-Wallet</li>
                    </ul>
                </div><!-- /.breadcrumb-inner -->
            </div><!-- /.container -->
        </div><!-- /.breadcrumb -->

        <div class="body-content outer-top-bd">
            <div class="container">
                <div class="checkout-box inner-bottom-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel-group checkout-steps" id="accordion">
                                <!-- checkout-step-01  -->
                                <div class="panel panel-default checkout-step-01">

                                    <!-- panel-heading -->
                                    <div class="panel-heading">
                                        <h4 class="unicase-checkout-title">
                                            <a data-toggle="collapse" class="" data-parent="#accordion" href="#collapseOne">
                                                <span>1</span>OFS e-Wallet
                                            </a>
                                        </h4>
                                    </div>
                                    <!-- panel-heading -->

                                    <div id="collapseOne" class="panel-collapse collapse in">

                                        <!-- panel-body  -->
                                        <div class="panel-body">
                                        <div class="wallet-container">

                                            <header class="wallet-header">
                                                <div class="wallet-balance">
                                                    <h3>Balance</h3>
                                                    <?php 
                                                    $query = mysqli_query($con, "
                                                    SELECT users.balance as balance FROM users WHERE id = '" . mysqli_real_escape_string($con, $_SESSION['id']) . "'");
                                                    $row=mysqli_fetch_array($query);
                                                    ?>
                                                    <h2>RM <?php echo $row['balance'] ?? '0'; ?></h2>
                                                </div>
                                                <div class="wallet-buttons">
                                                    <button class="topup" onclick="reloadBtn()">Reload</button>
                                                    <button class="withdraw" onclick="withdrawBtn()">Withdraw</button>
                                                </div>
                                            </header>
                                            <section class="topupsection" style="display:none;">
                                                <form method="post">
                                                    <div class="topup_title" style="margin-bottom:5px;">
                                                        <label for="topup_value" style="font-size:20px">Enter reload amount (Minimum RM10): </label>
                                                    </div>
                                                    <div class="topup_valuebox">
                                                        <div style="width:10%;text-align:center;align-self:center;font-size:20px;">RM </div>
                                                        <input required name="topup_value" class="topup_value" id="topup_value" type="number" min="10" max="1000000" style="border:none;">
                                                    </div>
                                                    <div class="quickbutton">
                                                        <button type="button" id="200btn" class="valuebtn" onclick="setTopupValue(200)">RM 200</button>
                                                        <button type="button" id="500btn" class="valuebtn" onclick="setTopupValue(500)">RM 500</button>
                                                        <button type="button" id="1000btn" class="valuebtn" onclick="setTopupValue(1000)">RM 1000</button>
                                                        <button type="button" id="otherbtn" class="valuebtn" onclick="setTopupValue('other')">Other</button>
                                                    </div>
                                                    <div class="topupbtn_div" style="height:40px;">
                                                        <input type="submit" value="RELOAD" name="topupsubmit" id="submitBtn" class="topupbtn">
                                                    </div>
                                                </form>
                                            </section>
	                                            <section class="withdrawsection" style="display:none;">
	                                                <form id="withdrawForm" method="post" onsubmit="validateAndSubmitWithdrawForm(event)">
	                                                    <input type="hidden" name="withdraw_reference" value="<?php echo htmlentities($wallet_withdraw_request_ref); ?>">
	                                                    <div class="wtdw_title" style="margin-bottom:5px;">
	                                                        <label for="withdraw_value" style="font-size:20px">Enter withdraw amount: </label>
	                                                    </div>
                                                    <div class="wtdw_valuebox">
                                                        <div style="width:10%;text-align:center;align-self:center;font-size:20px;">RM </div>
                                                        <input required name="wtdw_value" class="withdraw_value" id="withdraw_value" type="number" min="0.01" step="any" style="border:none;">
                                                    </div>
                                                    <div class="wtdw_title" style="margin-top:10px;margin-bottom:5px;">
                                                        <label style="font-size:16px">Card details are no longer stored locally. Withdrawal destination should be handled separately by your team or admin process.</label>
                                                    </div>
                                                    <div class="wtdwbtn_div" style="height:40px;">
                                                        <input type="submit" value="WITHDRAW" name="withdrawsubmit" id="withdrawsubmit" class="withdrawbtn"></input>
                                                    </div>
                                                </form>
                                            </section>

                                            <section class="wallet-transactions">
                                                <h3>Transaction List</h3>
                                                <table class="tsctable">
                                                    <thead>
                                                        <tr>
                                                            <th>Transaction Date</th>
                                                            <th>Order ID</th>
                                                            <th>Action</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $tscquery = mysqli_query($con, "
                                                        select transaction.transaction_date, transaction.id, transaction.order_id, transaction.user_id, transaction.action, transaction.amount, transaction.transaction_date from transaction where user_id = '" . mysqli_real_escape_string($con, $_SESSION['id']) . "' ORDER BY transaction_date DESC");
                                                        while($tscrow=mysqli_fetch_array($tscquery)){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $tscrow['transaction_date']; ?></td>
                                                            <td>
                                                                <?php
                                                                if($tscrow['order_id'] != NULL){ ?>
                                                                    <a href="javascript:void(0);" onClick="popUpWindow('track-order.php?oid=<?php echo htmlentities($tscrow['order_id']); ?>');" title="Track order">#<?php echo $tscrow['order_id']; ?></a></td>
                                                                <?php }else{ ?>
                                                                    N/A </td>
                                                                <?php } ?>
                                                                
                                                            <td><?php echo $tscrow['action']; ?></td>
                                                            <td><?php if($tscrow['action']=='Withdraw' || $tscrow['action']=='Pay'){ ?>
                                                                -
                                                            <?php } ?>
                                                                RM <?php echo $tscrow['amount']; ?></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </section>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php include ('includes/myaccount-sidebar.php'); ?>
                    </div><!-- /.row -->
                    <?php include ('includes/brands-slider.php'); ?>
                    <?php include ('includes/footer.php'); ?>

                </div>
            </div>
        </div>
            <script src="assets/js/jquery-1.11.1.min.js"></script>
            <script src="assets/js/bootstrap.min.js"></script>
            <script src="assets/js/owl.carousel.min.js"></script>
            <script src="assets/js/echo.min.js"></script>
            <script src="assets/js/jquery.easing-1.3.min.js"></script>
            <script src="assets/js/jquery.rateit.min.js"></script>
            <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
            <script src="assets/js/wow.min.js"></script>
            <script src="assets/js/scripts.js"></script>
            <script src="assets/js/password-validation-my-acc.js"></script>
            <script src="switchstylesheet/switchstylesheet.js"></script>

            <script>
                let reloadpage = document.querySelector('.topupsection'),
                withdrawpage = document.querySelector('.withdrawsection'),
                topupbutton = document.querySelector('.topup')
                withdrawbutton = document.querySelector('.withdraw'),
                btn200 = document.getElementById('200btn'),
                btn500 = document.getElementById('500btn'),
                btn1000 = document.getElementById('1000btn'),
                btnother = document.getElementById('otherbtn');
                
                function reloadBtn(){
                    reloadpage.style.display = "block";
                    withdrawpage.style.display = "none";
                    topupbutton.style.display = "none";
                    withdrawbutton.style.display = "block";
                }
                function withdrawBtn(){
                    reloadpage.style.display = "none";
                    withdrawpage.style.display = "block";
                    topupbutton.style.display = "block";
                    withdrawbutton.style.display = "none";
                }

                let firstbutton = document.querySelector('.valuebutton');
                function setTopupValue(value) {
                    var inputField = document.getElementById('topup_value');
                    if (value === 'other') {
                        inputField.value = '';
                        inputField.focus();
                        btnother.style.backgroundColor = "green";
                        btn200.style.backgroundColor = "white";
                        btn500.style.backgroundColor = "white";
                        btn1000.style.backgroundColor = "white";
                    } else if(value === 200) {
                        inputField.value = value;
                        btnother.style.backgroundColor = "white";
                        btn200.style.backgroundColor = "green";
                        btn500.style.backgroundColor = "white";
                        btn1000.style.backgroundColor = "white";
                    } else if(value === 500) {
                        inputField.value = value;
                        btnother.style.backgroundColor = "white";
                        btn200.style.backgroundColor = "white";
                        btn500.style.backgroundColor = "green";
                        btn1000.style.backgroundColor = "white";
                    } else if(value === 1000) {
                        inputField.value = value;
                        btnother.style.backgroundColor = "white";
                        btn200.style.backgroundColor = "white";
                        btn500.style.backgroundColor = "white";
                        btn1000.style.backgroundColor = "green";
                    }
                }

                function validateAndSubmitWithdrawForm(event) {
                var balance = <?php
                $query = mysqli_query($con, "SELECT balance FROM users WHERE id ='" . $_SESSION['id'] . "'");
                while ($row = mysqli_fetch_array($query)) {
                    echo $row['balance'];
                }
                ?>;
                var withdrawAmount = parseFloat(document.querySelector('input[name="wtdw_value"]').value);

                if (withdrawAmount > balance) {
                    alert('Balance insufficient. Please try again.');
                    event.preventDefault();
                    return false;
                }

                // If validation is successful, allow the form to submit
                return true;
            }
            </script>
    </body>

    </html>
<?php } ?>
