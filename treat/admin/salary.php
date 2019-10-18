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
  $person_id = ((isset($_POST['person_id']) && $_POST['person_id'] != '')?sanitize($_POST['person_id']):'');
  $pque= $db->query("SELECT * FROM persons WHERE id = $person_id");
  $per = mysqli_fetch_assoc($pque);

  $salaryQuery = $db->query("SELECT * FROM salary WHERE person_id = $person_id ORDER BY _date");
  $saltran = mysqli_fetch_assoc($salaryQuery);
  if(isset($_GET['sal'])){
    $person_name = ((isset($_POST['person_name']) && $_POST['person_name'] != '')?sanitize($_POST['person_name']):'');
    $amount = ((isset($_POST['amount']) && $_POST['amount'] != '')?sanitize($_POST['amount']):'');
    $task = ((isset($_POST['task']) && $_POST['task'] != '')?sanitize($_POST['task']):'');

    $required = array('person_name','amount','task');

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
    $insSql = "INSERT INTO salary (`person_id`,`amount`,`task`)
    VALUES ('$person_id','$amount','$task')";
      $db->query($insSql);
    if($task == 'Paid'){
    $exinsSql = "INSERT INTO expenses (`us_reason`,`person_name`,`amount`)
    VALUES ('Salary','$person_name','$amount')";
    $db->query($exinsSql);
  }
  }
  $_SESSION['success_flash'] = 'Record has been updated!';
  header('Location: coworkers.php');
}

$amt_paid=0;
$tot_sal = 0;

?>
<div class="row">
<h2 class="text-center">Salary</h2><hr>
<form action="salary.php?sal=1" method="post" enctype="multipart/form-data">
  <input type="hidden" name="person_id" value="<?php echo($person_id);?>">
  <div class="form-group col-md-3">
    <label for="person_name">person's Name*:</label>
    <input type="text" name="person_name" id="name" class="form-control" value="<?php echo($per['person_name']);?>" readonly>
  </div>
  <div class="form-group col-md-3">
    <label for="amount">Amount*:</label>
    <input type="text" id="amount" name="amount" class="form-control">
  </div>

<div class="form-group col-md-3">
  <label for="task">Task*:</label>
  <select class="form-control" name="task">
    <option value=""></option>
    <?php if(has_permission()):?>
      <option value="Paid">Paid</option>
      <option value="Create salary">Create salary</option>
    <?php endif;?>
  </select>
</div>
  <div class="form-group  text-right pull-right " style="margin-top:22px;margin-right:30px;padding: 6px 16px;font-size: 15px;">
    <input type="submit" value="Done" class="btn btn-info">
    <div class="clearfix"></div>
  </div>
</form>
</div>

<div>
  <div>
<h2 class="text-center">Salary details</h2>
</div>
<hr>
<table class="table table-bordered table-striped table-condensed">
  <thead class="text-center"><th>#</th><th class="text-center">Name</th><th class="text-center">Amount paid</th><th class="text-center">Salary</th><th class="text-center">Date</th></thead>
  <tbody class="text-center">
    <?php
    $i=0;
     while($saltran = mysqli_fetch_assoc($salaryQuery)):
       $i=$i+1;?>
      <tr>
        <td><?php echo($i);?></td>
        <td><?php echo($per['person_name']);?></td>
        <?php if($saltran['task'] == "Paid"){ $amt_paid= ($amt_paid+$saltran['amount']);?>
        <td><?php echo(money($saltran['amount']));?></)td>
        <?php }else{?>
        <td><?php echo('');?></td>
        <?php }?>
        <?php if($saltran['task'] == "Create salary"){($tot_sal = $tot_sal+$saltran['amount']);?>
        <td><?php echo(money($saltran['amount']));?></td>
        <?php } else{?>
        <td><?php echo('');?></td>
        <?php }?>
        <td><?php echo($saltran['_date']);?></td>
      </tr>
    <?php endwhile;?>

  </tbody>
</table>
<div class="text-right">
  <h4>Total Amount paid: <?php echo($amt_paid);?><span>&nbsp;&nbsp;&nbsp;&nbsp;</span> Total salary: <?php echo($tot_sal);?></h4>
  <h4>Salary to be paid: <?php echo($tot_sal-$amt_paid);?></h4>
</div>
</div>
<?php  include 'includes/footer.php';
?>
