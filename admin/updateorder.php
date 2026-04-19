<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
  header('location:index.php');
} else {
  $oid = intval($_GET['oid']);
  date_default_timezone_set('Asia/Kuala_Lumpur');// change according timezone
  $currentTime = date('d-m-Y h:i:s A', time());
  if (isset($_POST['submit2'])) {
    $status = $_POST['status'];
    $remark = $_POST['remark'];//space char 

    $query = mysqli_query($con, "insert into ordertrackhistory(orderId,status,remark) values('$oid','$status','$remark')");
    $sql = mysqli_query($con, "update orders set orderStatus='$status' where id='$oid'");
    echo "<script>alert('Order updated sucessfully!');</script>";
    echo "<script type='text/javascript'>history.back();</script>";
    //}
  }

  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Order Details</title>
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="css/updateorder.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
      rel='stylesheet'>
    <script src="https://kit.fontawesome.com/4a07c4d5e3.js" crossorigin="anonymous"></script>
    <script language="javascript" type="text/javascript">
      var popUpWin = 0;
      function popUpWindow(URLStr, left, top, width, height) {
        if (popUpWin) {
          if (!popUpWin.closed) popUpWin.close();
        }
        popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width=' + 600 + ',height=' + 600 + ',left=' + left + ', top=' + top + ',screenX=' + left + ',screenY=' + top + '');
      }

    </script>
  </head>

  <body>
    <?php include ('include/header.php'); ?>

    <div class="wrapper">
      <div class="container">
        <div class="row">
          <?php include ('include/sidebar.php'); ?>
          <div class="span9">
            <div class="content">
              <div class="module">
                <div class="module-head">
                  <h3>Order Details and Order Tracking History</h3>
                </div>
                <div class="module-body table">
                  <?php if (isset($_GET['del'])) { ?>
                    <div class="alert alert-error">
                      <button type="button" class="close" data-dismiss="alert">×</button>
                      <strong>Oh snap!</strong>
                      <?php echo htmlentities($_SESSION['delmsg']); ?>     <?php echo htmlentities($_SESSION['delmsg'] = ""); ?>
                    </div>
                  <?php } ?>

                  <br />

                  <?php
                  $query = mysqli_query($con, "select 
                      orders.subtotal,
                      orders.shippingCharge,
                      orders.grandtotal,
                      orders.paymentMethod,
                      orders.shippingReceiver,
                      orders.billingReceiver,
                      users.name,
                      users.contactno,
                      users.email as useremail,
                      orders.shippingPhone,
                      orders.billingPhone,
                      orders.shippingAddress as shippingaddress,
                      orders.billingAddress as billingaddress,
                      orders.orderDate as orderdate,
                      orders.orderStatus as orderStatus,
                      orders.id as id  from orders join users on  orders.userId=users.id");

                  while ($row = mysqli_fetch_array($query)) {
                    if ($oid == $row['id']) { ?>

                      <table cellpadding="0" style="width:90%;margin-left:auto;margin-right:auto;" cellspacing="0"
                        class="table table-bordered display table-responsive">
                        <thead>
                          <tr height="40" style="background:#f8f8f8;">
                            <th style="font-size:20px;color:#555;margin: auto;width: 50%;padding: 10px;" colspan="2"><b><i
                                  class="fa-solid fa-circle-info"></i> Order ID #<?php echo htmlentities($row['id']); ?></b>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="left-table"><b>Order By: </b></td>
                            <td class="right-table">
                              <p class="right-data"><?php echo $row['name'] ?></p>
                              <p class="right-data"><?php echo $row['useremail'] ?></p>
                              <p class="right-data"><?php echo $row['contactno'] ?></p>
                            </td>
                          </tr>
                          <tr>
                            <td class="left-table"><b>Order At: </b></td>
                            <td class="right-table"><?php echo $row['orderdate'] ?></td>
                          </tr>
                          <tr>
                            <td class="left-table" colspan="2"><b>Ordered Product: </b></td>

                          </tr>
                          <?php $orderdetailsquery = mysqli_query($con, "SELECT 
                            products.id AS product_id,
                            products.productName AS product_name,
                            products.productImage1 AS product_image,
                            orders.id AS order_id,
                            orderdetails.orderId AS orderdetail_order_id,
                            orderdetails.productId AS orderdetail_product_id,
                            orderdetails.quantity AS quantity
                        FROM 
                            orderdetails
                        JOIN 
                            orders ON orderdetails.orderId = orders.id
                        JOIN 
                            products ON orderdetails.productId = products.id;
                        ");
                          while ($detailsrow = mysqli_fetch_array($orderdetailsquery)) {
                            if ($oid == $detailsrow['order_id']) { ?>
                              <tr>
                                <td colspan="2" class="right-table"><img
                                    src="productimages/<?php echo htmlentities($detailsrow['product_id']); ?>/<?php echo htmlentities($detailsrow['product_image']); ?>"
                                    width="150px" height="150px">
                                  <?php echo $detailsrow['product_name'] ?> x<?php echo $detailsrow['quantity'] ?></td>
                              </tr>
                            <?php }
                          } ?>
                          <tr>
                            <td class="left-table"><b>Subtotal: </b></td>
                            <td class="right-table">RM <?php echo $row['subtotal'] ?></td>

                          </tr>
                          <tr>
                            <td class="left-table"><b>Shipping Fee: </b></td>
                            <td class="right-table">RM <?php echo $row['shippingCharge'] ?></td>

                          </tr>
                          <tr>
                            <td class="left-table"><b>Grandtotal: </b></td>
                            <td class="right-table">RM <?php echo $row['grandtotal'] ?></td>

                          </tr>
                          <tr>
                            <td class="left-table"><b>Paid By: </b></td>
                            <td class="right-table"><?php echo $row['paymentMethod'] ?></td>
                          </tr>
                          <tr>
                            <td class="left-table"><b>Order Status: </b></td>
                            <td class="right-table"><?php echo $row['orderStatus'] ?></td>
                          </tr>
                          <tr>
                            <td class="left-table"><b>Shipping Information: </b></td>
                            <td class="right-table">
                              <p class="right-data"><?php echo $row['shippingReceiver'] ?>,</p>
                              <p class="right-data"><?php echo $row['shippingaddress'] ?>,</p>
                              <p class="right-data"><?php echo $row['shippingPhone'] ?></p>
                            </td>
                          </tr>
                          <tr>
                            <td class="left-table"><b>Billing Information: </b></td>
                            <td class="right-table">
                              <p class="right-data"><?php echo $row['billingReceiver'] ?>,</p>
                              <p class="right-data"><?php echo $row['billingaddress'] ?>,</p>
                              <p class="right-data"><?php echo $row['billingPhone'] ?></p>
                            </td>
                          </tr>

                        <?php }
                  } ?>
                    </tbody>
                  </table>
                  <br>
                  <form name="updateticket" id="updateticket" method="post">

                    <table width="100%" style="width:90%;margin-left:auto;margin-right:auto;" cellspacing="0"
                      cellpadding="0" class="table table-bordered display table-responsive">

                      <tr height="40" style="background:#f8f8f8;">
                        <td colspan="2" style="padding:10px;">
                          <div><b style="font-size:20px; text-align:left;margin: auto;width: 50%;"><i
                                class="fa-solid fa-clock-rotate-left"></i> Order Tracking History</b></div>
                        </td>
                      </tr>
                      <?php
                      $ret = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid'");
                      while ($row = mysqli_fetch_array($ret)) {
                        ?>
                        <tr height="20">
                          <td class="left-table"><b>Posting Date/Time: </b></td>
                          <td class="right-table"><?php echo $row['postingDate']; ?></td>
                        </tr>
                        <tr height="20">
                          <td class="left-table"><b>Order Status: </b></td>
                          <td class="right-table"><?php echo $row['status']; ?></td>
                        </tr>
                        <tr height="20">
                          <td class="left-table"><b>Remark: </b></td>
                          <td class="right-table"><?php echo $row['remark']; ?></td>
                        </tr>

                        <tr>
                          <td colspan="2">
                          </td>
                        </tr>
                      <?php } ?>
                      <?php
                      $st = 'Cancelled';
                      $rt = mysqli_query($con, "SELECT * FROM orders WHERE id='$oid'");
                      while ($num = mysqli_fetch_array($rt)) {
                        $currrentSt = $num['orderStatus'];
                      }
                      if ($st == $currrentSt) { ?>
                        <tr>
                          <td colspan="2" class="left-table"><b>
                              &#x274c; Order cancelled. </b></td>
                        <?php } else {
                        ?>

                        <tr height="50">
                          <td class="left-table"><b>Status: </b></td>
                          <td class="right-table"><span class="">
                              <select name="status" class="right-data" style="width:100%; height:100%;" required="required">
                                <option value="">Select Status</option>
                                <option value="In Process">In Process</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                              </select>
                            </span></td>
                        </tr>

                        <tr>
                          <td class="left-table"><b>Remark: </b></td>
                          <td class="right-table"><span class="fontkink">
                              <textarea rows="8" class="right-data" style="width:97.5%;height:100%;" name="remark"
                                required="required"></textarea>
                            </span></td>
                        </tr>
                        <td colspan="2" class="fontkink">
                          <input class="submitButton" type="submit" name="submit2" value="UPDATE SHIPPING INFORMATION"
                            size="40" style="cursor: pointer;" />
                        </td>
                        </tr>
                      <?php } ?>
                    </table>
                  </form>
                  <button class="gobackButton" onclick="javascript: history.go(-1)">Go Back</button>

                </div>
              </div>
            </div><!--/.content-->
          </div><!--/.span9-->
        </div>
      </div><!--/.container-->
    </div><!--/.wrapper-->

    <?php include ('include/footer.php'); ?>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="scripts/datatables/jquery.dataTables.js"></script>
    <script>
      $(document).ready(function () {
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass("btn-group datatable-pagination");
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
      });
    </script>
  </body>
<?php } ?>
