<?php
require_once __DIR__ . '/includes/customer_session.php';
include_once 'includes/config.php';
$oid = intval($_GET['oid']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/trackorder.css">
  <link rel="shortcut icon" href="assets/images/favicon.ico">
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

    function closeWindow() {
      window.close();
    }

  </script>
</head>

<body>
  <div class="container">
    <div class="logo">
      <a href="index.php"><img src="assets/images/logo/main-logo.png" alt="Online Furniture Store Logo"></a>

    </div>
    <div class="content">
      <div class="card">
        <h2>Order Information</h2>
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
                      orders.id as id from orders join users on  orders.userId=users.id");

        while ($row = mysqli_fetch_array($query)) {
          if ($oid == $row['id']) { ?>
            <table cellpadding="0">
              <thead>
                <tr height="40" style="background:#f8f8f8;">
                  <th style="font-size:20px;color:#555;margin: auto;width: 50%;padding: 10px;" colspan="2"><b><i
                        class="fa-solid fa-circle-info"></i> Order ID #<?php echo htmlentities($row['id']); ?></b></th>
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
                          src="admin/productimages/<?php echo htmlentities($detailsrow['product_id']); ?>/<?php echo htmlentities($detailsrow['product_image']); ?>"
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
        </table>
      </div>
      <div class="card">
        <h2>Order Tracking Details</h2>
        <table>
          <?php
          $ret = mysqli_query($con, "SELECT * FROM ordertrackhistory WHERE orderId='$oid'");
          $num = mysqli_num_rows($ret);
          if ($num > 0) {
            while ($row = mysqli_fetch_array($ret)) {
              ?>
              <tr>
                <td>At Date:</td>
                <td><?php echo $row['postingDate']; ?></td>
              </tr>
              <tr>
                <td>Status:</td>
                <td><?php echo $row['status']; ?></td>
              </tr>
              <tr>
                <td>Remark:</td>
                <td><?php echo $row['remark']; ?></td>
              </tr>
              <tr>
                <td colspan="2">
                  <hr>
                </td>
              </tr>
            <?php }
          } else {
            ?>
            <tr>
              <td colspan="2"><b>Status: </b>Your order is currently in the process, come back later for more tracking
                information.</td>
            </tr>
          <?php }
          $st = 'Delivered';
          $rt = mysqli_query($con, "SELECT * FROM orders WHERE id='$oid'");
          while ($num = mysqli_fetch_array($rt)) {
            $currrentSt = $num['orderStatus'];
          }
          if ($st == $currrentSt) { ?>
            <tr>
              <td colspan="2"><b>Product Delivered successfully</b></td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>
    <div class="button-container">
      <button onclick="closeWindow()">Go Back</button>
    </div>
  </div>
</body>

</html>
