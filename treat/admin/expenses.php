<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
  if(!is_logged_in()){
    login_error_redirect();
  }

  if((!has_permission())){
    permission_error_redirect('index.php');
  }

  include 'includes/head.php';
  include 'includes/navigation.php';
  //echo $_SESSION['SBperson'];

  if(isset($_GET['exp'])){
    $ot_reason = ((isset($_POST['ot_reason']) && $_POST['ot_reason'] != '')?sanitize($_POST['ot_reason']):'');
    $amount = ((isset($_POST['amount']) && $_POST['amount'] != '')?sanitize($_POST['amount']):'');

    $required = array('ot_reason','amount');

  foreach($required as $f){
    if(empty($_POST[$f]) ){
      echo($f);
      $errors[] = 'All fields with an astrisk(*) are required.';
      break;
    }
  }

  if(!empty($errors)){
    echo display_errors($errors);
  }
  else{
    $insSql = "INSERT INTO expenses (`ot_reason`,`amount`)
    VALUES ('$ot_reason','$amount')";
  }
  $db->query($insSql);
  $_SESSION['success_flash'] = 'Record has been updated!';
  header('Location: index.php');
}

?>
<div class="row">
<h2 class="text-center">Expenses</h2><hr>
<form action="expenses.php?exp=1" method="post" enctype="multipart/form-data">
  <div class="form-group col-md-4">
    <label for="ot_reason">Paid for*:</label>
    <input type="text" name="ot_reason" id="ot_reason" class="form-control" placeholder="Reason">
  </div>
  <div class="form-group col-md-3">
    <label for="amount">Amount*:</label>
    <input type="text" id="amount" name="amount" class="form-control">
  </div>

  <div class="form-group " style="margin-top:22px;margin-right:30px;padding: 6px 20px;font-size: 15px;">
    <input type="submit" value="Submit" class="btn btn-info" style="margin-top:18px;">
    <div class="clearfix"></div>
  </div>
</form>
</div>

<?php  include 'includes/footer.php';
?>
