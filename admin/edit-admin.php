<?php
require_once __DIR__ . '/include/admin_session.php';
include ('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $pid = intval($_GET['id']); // admin id

    // Update admin status and username
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $status = $_POST['Status'];

        // Check if the username contains only alphabets and spaces
        if (!preg_match('/^[a-zA-Z ]+$/', $username)) {
            $_SESSION['error'] = "Username can only contain alphabets and spaces. Update failed.";
        } else {
            $sql = mysqli_query($con, "UPDATE admin SET username='$username', Status='$status' WHERE id='$pid'");
            if ($sql) {
                $_SESSION['msg'] = "Admin Updated Successfully!!";
            } else {
                $_SESSION['error'] = "Admin update failed. Please try again.";
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin | Update Admin</title>
        <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
        <link type="text/css" href="css/theme.css" rel="stylesheet">
        <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
        <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600'
            rel='stylesheet'>
        <link rel="shortcut icon" href="images/favicon.ico">
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
                                    <h3>Edit Admin</h3>
                                </div>
                                <div class="module-body">

                                    <?php if (isset($_POST['submit'])) { ?>
                                        <?php if (isset($_SESSION['error'])) { ?>
                                            <div class="alert alert-danger">
                                                <button type="button" class="close" data-dismiss="alert">×</button>
                                                <strong>Error!</strong> <?php echo htmlentities($_SESSION['error']); ?>
                                            </div>
                                            <?php unset($_SESSION['error']);
                                        } else { ?>
                                            <div class="alert alert-success">
                                                <button type="button" class="close" data-dismiss="alert">×</button>
                                                <strong>Well done!</strong> <?php echo htmlentities($_SESSION['msg']); ?>
                                                <?php echo htmlentities($_SESSION['msg'] = ""); ?>
                                            </div>
                                        <?php }
                                    } ?>

                                    <br />

                                    <form class="form-horizontal row-fluid" name="updateadmin" method="post">
                                        <?php
                                        $query = mysqli_query($con, "SELECT * FROM admin WHERE id='$pid'");
                                        while ($row = mysqli_fetch_array($query)) {
                                            ?>

                                            <div class="control-group">
                                                <label class="control-label" for="basicinput">Username</label>
                                                <div class="controls">
                                                    <input type="text" name="username" placeholder="Enter Username"
                                                        value="<?php echo htmlentities($row['username']); ?>" class="span8 tip"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="basicinput">Status</label>
                                                <div class="controls">
                                                    <select name="Status" class="span8 tip" required>
                                                        <?php echo htmlentities($row['Status']); ?>
                                                        <option value="Active" <?php if ($row['Status'] == 'Active')
                                                            echo 'selected'; ?>>Active</option>
                                                        <option value="Inactive" <?php if ($row['Status'] == 'Inactive')
                                                            echo 'selected'; ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                        <?php } ?>
                                        <div class="control-group">
                                            <div class="controls">
                                                <button type="submit" name="submit" class="btn">Update</button>
                                            </div>
                                        </div>
                                    </form>
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
    </body>

    </html>
<?php } ?>
