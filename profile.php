<?php include('head.php'); ?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('connect.php'); ?>

<?php
if (isset($_POST["btn_update"])) {
  extract($_POST);

  $target_dir = "uploadImage/Profile/";
  $image1 = basename($_FILES["image"]["name"]);
  if ($_FILES["image"]["tmp_name"] != '') {
    $image = $target_dir . $image1;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
      @unlink("uploadImage/Profile/" . $_POST['old_image']);
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  } else {
    $image1 = $_POST['old_image'];
  }

  $q1 = "UPDATE `admin` SET `fname`='$fname', `lname`='$lname', `email`='$email', `contact`='$contact', `dob`='$dob', `gender`='$gender', `image`='$image1' WHERE id = '" . $_SESSION["id"] . "'";

  if ($conn->query($q1) === TRUE) {
    $_SESSION['success'] = 'Record Successfully Updated';
    echo '<script type="text/javascript">window.location = "profile.php";</script>';
  } else {
    $_SESSION['error'] = 'Something Went Wrong';
  }
}

$que = "SELECT * FROM admin WHERE id = '" . $_SESSION["id"] . "'";
$query = $conn->query($que);
$row = mysqli_fetch_array($query);

$fname = $row['fname'];
$lname = $row['lname'];
$email = $row['email'];
$contact = $row['contact'];
$dob1 = $row['dob'];
$gender = $row['gender'];
$image = !empty($row['image']) ? $row['image'] : 'default.png';
?>

<style>
  .profile-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
  }
</style>

<div class="page-wrapper">
  <div class="row page-titles">
    <div class="col-md-5 align-self-center">
      <h3 class="text-primary">Profile</h3>
    </div>
    <div class="col-md-7 align-self-center">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
        <li class="breadcrumb-item active">Profile</li>
      </ol>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-8" style="margin-left: 10%;">
        <div class="card">
          <div class="card-body">
            <form class="form-horizontal" method="POST" enctype="multipart/form-data">
              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">First Name</label>
                  <div class="col-sm-9">
                    <input type="text" value="<?php echo $fname; ?>" name="fname" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">Last Name</label>
                  <div class="col-sm-9">
                    <input type="text" value="<?php echo $lname; ?>" name="lname" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">Email</label>
                  <div class="col-sm-9">
                    <input type="email" value="<?php echo $email; ?>" name="email" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">Gender</label>
                  <div class="col-sm-9">
                    <select name="gender" class="form-control" required>
                      <option value="Male" <?php if ($gender == "Male") echo "selected"; ?>>Male</option>
                      <option value="Female" <?php if ($gender == "Female") echo "selected"; ?>>Female</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">Date Of Birth</label>
                  <div class="col-sm-9">
                    <input type="date" value="<?php echo $dob1; ?>" name="dob" class="form-control">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">Contact</label>
                  <div class="col-sm-9">
                    <input type="text" value="<?php echo $contact; ?>" name="contact" class="form-control" maxlength="10" required>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <label class="col-sm-3 control-label">Image</label>
                  <div class="col-sm-9">
                    <img src="uploadImage/Profile/<?php echo $image; ?>" alt="Profile" class="profile-circle">
                    <input type="hidden" value="<?php echo $image; ?>" name="old_image">
                    <input type="file" class="form-control" name="image">
                  </div>
                </div>
              </div>

              <button type="submit" name="btn_update" class="btn btn-primary btn-flat m-b-30 m-t-30">Update</button>
            </form>
          </div>
        </div>
      </div>
    </div>

<?php include('footer.php'); ?>

<link rel="stylesheet" href="popup_style.css">
<?php if (!empty($_SESSION['success'])) { ?>
  <div class="popup popup--icon -success js_success-popup popup--visible">
    <div class="popup__background"></div>
    <div class="popup__content">
      <h3 class="popup__content__title">Success</h3>
      <p><?php echo $_SESSION['success']; ?></p>
      <p>
        <button class="button button--success" data-for="js_success-popup">Close</button>
      </p>
    </div>
  </div>
  <?php unset($_SESSION["success"]);
} ?>

<?php if (!empty($_SESSION['error'])) { ?>
  <div class="popup popup--icon -error js_error-popup popup--visible">
    <div class="popup__background"></div>
    <div class="popup__content">
      <h3 class="popup__content__title">Error</h3>
      <p><?php echo $_SESSION['error']; ?></p>
      <p>
        <button class="button button--error" data-for="js_error-popup">Close</button>
      </p>
    </div>
  </div>
  <?php unset($_SESSION["error"]);
} ?>

<script>
  var addButtonTrigger = function addButtonTrigger(el) {
    el.addEventListener('click', function() {
      var popupEl = document.querySelector('.' + el.dataset.for);
      popupEl.classList.toggle('popup--visible');
    });
  };

  Array.from(document.querySelectorAll('button[data-for]')).forEach(addButtonTrigger);
</script>
