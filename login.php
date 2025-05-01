<?php
session_start();
include('head.php');
include('connect.php');
?>
<link rel="stylesheet" href="popup_style.css">

<?php
if (isset($_POST['btn_login'])) {
  $unm = $_POST['email'];
  $inputPassword = $_POST['password'];

  // Salt + hash
  $salt = '2123293dsj2hu2nikhiljdsd';
  $pass = hash('sha256', $salt . hash('sha256', $inputPassword));

  // Use prepared statements to avoid SQL injection
  $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
  $stmt->bind_param("ss", $unm, $pass);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    // Set session
    $_SESSION["id"] = $row['id'];
    $_SESSION["username"] = $row['username'];
    $_SESSION["password"] = $row['password'];
    $_SESSION["email"] = $row['email'];
    $_SESSION["fname"] = $row['fname'];
    $_SESSION["lname"] = $row['lname'];
    $_SESSION["image"] = $row['image'];
    ?>
    <div class="popup popup--icon -success js_success-popup popup--visible">
      <div class="popup__background"></div>
      <div class="popup__content">
        <h3 class="popup__content__title">Success</h3>
        <p>Login Successfully</p>
        <p><?php echo "<script>setTimeout(\"location.href = 'index.php';\",1500);</script>"; ?></p>
      </div>
    </div>
  <?php
  } else {
  ?>
    <div class="popup popup--icon -error js_error-popup popup--visible">
      <div class="popup__background"></div>
      <div class="popup__content">
        <h3 class="popup__content__title">Error</h3>
        <p>Invalid Email or Password</p>
        <p>
          <a href="login.php"><button class="button button--error" data-for="js_error-popup">Close</button></a>
        </p>
      </div>
    </div>
  <?php
  }
}
?>

<div id="main-wrapper">
  <div class="unix-login">
    <?php
    $sql_login = "SELECT * FROM manage_website";
    $result_login = $conn->query($sql_login);
    $row_login = mysqli_fetch_array($result_login);
    ?>
    <div class="container-fluid" style="background-image: url('uploadImage/Logo/<?php echo $row_login['background_login_image']; ?>');">
      <div class="row justify-content-center">
        <div class="col-lg-4">
          <div class="login-content card">
            <div class="login-form">
              <center><img src="uploadImage/Logo/1.png" style="width:50%;"></center><br>
              <form method="POST">
                <div class="form-group">
                  <label>Email address</label>
                  <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="checkbox">
                  <label class="pull-right">
                    <a href="forgot_password.php">Forgotten Password?</a>
                  </label>
                </div>
                <button type="submit" name="btn_login" class="btn btn-primary btn-flat m-b-30 m-t-30">Sign in</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- JS Scripts -->
<script src="js/lib/jquery/jquery.min.js"></script>
<script src="js/lib/bootstrap/js/popper.min.js"></script>
<script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
<script src="js/jquery.slimscroll.js"></script>
<script src="js/sidebarmenu.js"></script>
<script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
<script src="js/custom.min.js"></script>
</body>
</html>
