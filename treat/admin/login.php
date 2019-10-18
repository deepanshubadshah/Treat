<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(is_logged_in()){
  header('Location: index.php');
}
include 'includes/head.php';

$username = ((isset($_POST['username']))?sanitize($_POST['username']):'');
$username = trim($username);
$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
$password = trim($password);
//$hashed = password_hash($password, PASSWORD_DEFAULT);
$errors = array();
?>

<style>
  body{
    height:590px;
  	background-image:url("/treat/images/sz/pic/ch.png");
    background-repeat: no-repeat;
  	background-size: 100% 670px;

   -/*webkit-filter: blur(5px);
    filter:blur(5px);
    background-size: 100vw 100vh*/
  }
</style>
<div id="login-form">
  <div>

    <?php
      if($_POST){
        //form validation
        if((empty($_POST['username']) && empty($_POST['mobile_no'])) || empty($_POST['password'])){
          $errors[]= 'You must provide username/mobile number and password';
        }

        //validate Email
      /*  if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
          $errors[] = 'you must enter a valid email';
        }*/

        // password length is more than 6 char
        if(strlen($password)<6){
          $errors[] = 'Password must be at least 6 characters long';
        }

        // check if email exists in the database
        $query = $db->query("SELECT * FROM persons WHERE username = '$username'");
        $person = mysqli_fetch_assoc($query);
        $personCount = mysqli_num_rows($query);
        if($personCount < 1){
          $errors[] = 'This username doesn\'t exist in our database';
        }

        if(!password_verify($password, $person['password'])){
          $errors[] = 'The password does not match our record';
        }

        //check for errormsg
        if(!empty($errors)){
          echo display_errors($errors);
        }
        else{
          //log person in
          $person_id = $person['id'];
          login($person_id);
        }
      }
    ?>

  </div>
  <h2 class="text-center">Login</h2><hr>
  <form action="login.php" method="post">
    <div class="form-group">
      <label for="username">Username:</label>
      <input type="text" name="username" id="username" class="form-control" value="<?php echo($username);?>">
    </div>
    <div class="form-group">
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" class="form-control" value="<?php echo($password);?>">
    </div>
    <div class="form-group">
      <input type="submit" value="Login" class="btn btn-primary">
    </div>
  </form>
  <p class="text-right "><a href="#" alt="home">Visit Site</a></p>
</div>
<?php include 'includes/footer.php'; ?>
