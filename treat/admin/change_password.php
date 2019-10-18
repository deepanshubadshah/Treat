<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}

include 'includes/head.php';

$hashed = $person_data['password'];
$old_password = ((isset($_POST['old_password']))?sanitize($_POST['old_password']):'');
$old_password = trim($old_password);
$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
$password = trim($password);
$confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
$confirm = trim($confirm);
$new_hashed = password_hash($password, PASSWORD_DEFAULT);
$person_id = $person_data['id'];
$errors = array();
?>

<div id="login-form">
  <div>

    <?php
      if($_POST){
        //form validation
        if(empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])){
          $errors[]= 'You must fill out all fields';
        }

        // password length is more than 6 char
        if(strlen($password)<6){
          $errors[] = 'Password must be at least 6 characters long';
        }
        // if new password is same as confirm
        if($password != $confirm){
          $errors[] = 'The new password and confirm new password does not match';
        }


        if(!password_verify($old_password, $hashed)){
          $errors[] = 'Your old password does not match our records.';
        }

        //check for errormsg
        if(!empty($errors)){
          echo display_errors($errors);
        }
        else{
          //change password
          $db->query("UPDATE persons SET password = '$new_hashed' WHERE id='$person_id'");
          $_SESSION['success_flash'] = 'Your password has been updated!';
          header('Location: index.php');
        }
      }
    ?>

  </div>
  <h2 class="text-center">Change Password</h2><hr>
  <form class="col-md" action="change_password.php" method="post">
    <div class="form-group">
      <label for="old_password">Old Password:</label>
      <input type="password" name="old_password" id="old_password" class="form-control" value="<?php echo($old_password);?>">
    </div>
    <div class="form-group">
      <label for="password">New Password:</label>
      <input type="password" name="password" id="password" class="form-control" value="<?php echo($password);?>">
    </div>
    <div class="form-group">
      <label for="confirm">Confirm New Password:</label>
      <input type="password" name="confirm" id="confirm" class="form-control" value="<?php echo($confirm);?>">
    </div>
    <div class="form-group">
      <a href="index.php" class="btn btn-default">Cancel</a>
      <input type="submit" value="Login" class="btn btn-primary">
    </div>
  </form>
  <p class="text-right "><a href="/treat/index.php" alt="home">Visit Site</a></p>
</div>
<?php include 'includes/footer.php'; ?>
