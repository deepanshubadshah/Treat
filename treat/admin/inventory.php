<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';

if(!is_logged_in()){
  login_error_redirect();
}
if((!has_permission()) && (!has_permission_editor())){
  permission_error_redirect('index.php');
}


include 'includes/head.php';
include 'includes/navigation.php';

//Delete inventory
if(isset($_GET['delete'])){
  if((!has_permission())){
    permission_error_redirect('index.php');
    exit();
  }
  $id = sanitize($_GET['delete']);
  $db->query("UPDATE inventory SET deleted = 1 WHERE id = '$id'");
  header('Location: inventory.php');
}

//Inv Consumption
if(isset($_GET['cons'])){
  $cqid = sanitize($_GET['cons']);
  $cinventoryResults = $db->query("SELECT * FROM inventory WHERE id ='$cqid'");
  $cinventory = mysqli_fetch_assoc($cinventoryResults);

  $cquantity = ((isset($_POST['cquantity']) && $_POST['cquantity'] != '')?sanitize($_POST['cquantity']):'');

  $ctitle = $cinventory['title'];
  $csquantity = $cinventory['quantity'];
  $cthreshold = $cinventory['threshold'];
  $cunit = $cinventory['unit'];

  if($_POST) {
  if($_POST['cquantity'] == ''){
    //echo $field;
    $errors[] = 'Please enter quantity to be weigh out !';
  }

  if($cinventory['quantity'] < $cquantity){
    $errors[] = 'Only '.$cinventory['quantity'].' '.$cinventory['unit'].' are available!';
  }

  if(!empty($errors)){
    echo display_errors($errors);
  }
  else{
  $per_id =  $person_data['id'];
  $cqupdate = $cinventory['quantity'] - $cquantity;
  $db->query("UPDATE inventory SET quantity = $cqupdate WHERE id = '$cqid'");
  $db->query("INSERT INTO inventory_cons (`inventory_id`, `quantity`, `person_id`) VALUES ('$cqid', '$cquantity','$per_id')");
  header('Location: inventory.php');
}}
?>
<h2 class="text-center">Weigh inventory</h2><hr>
<form action="inventory.php?<?php echo((isset($_GET['cons']))?'cons='.$cqid:'');?>" method="POST" enctype="multipart/form-data">
  <div class="form-group col-md-3">
    <label for="title">Title:</label>
    <input type="text" name="title" class="form-control" id="title" value="<?php echo $ctitle;?>" readonly>
  </div>
  <div class="form-group col-md-3">
    <label for="quantity">Quantity available:</label>
    <input type="text" id="quantity" name="quantity" class="form-control" value="<?php echo ($csquantity.' '.$cunit);?>" readonly>
  </div>
  <div class="form-group col-md-3">
    <label for="cquantity">Quantity(<?php echo('in '.$cunit);?>)*:</label>
    <input type="text" id="cquantity" name="cquantity" class="form-control" value="<?php echo $cquantity;?>" >
  </div>
  <div class="form-group col-md-2">
    <label for="threshold">Threshold(<?php echo('in '.$cunit);?>):</label>
    <input type="number" id="threshold" name="threshold" class="form-control" value="<?php echo ($cthreshold);?>" readonly>
  </div>
  <div class="row" style="margin-left: -1px;">
  <div class="form-group col-md-6"></div>
</div>
  <div class="form-group pull-right">
    <a href="inventory.php" class="btn btn-default">Cancel</a>
    <input type="submit" value="Weigh out inventory" class="btn btn-success">
  </div><div class="clearfix"></div>
</form>

<?php
}

else if (isset($_GET['add']) || isset($_GET['edit'])){

  if((!has_permission())){
    permission_error_redirect('index.php');
    exit();
  }

$title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):'');
$price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):'');
$quantity = ((isset($_POST['quantity']) && $_POST['quantity'] != '')?sanitize($_POST['quantity']):'');
$price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):'');
$threshold = ((isset($_POST['threshold']) && $_POST['threshold'] != '')?sanitize($_POST['threshold']):'');
$unit = ((isset($_POST['unit']) && $_POST['unit'] != '')?sanitize($_POST['unit']):'');
$description = ((isset($_POST['description']) && $_POST['description'] != '')?sanitize($_POST['description']):'');
$pupdate = 0;
$qupdate = 0;
$inventory_consumption = 0;

if(isset($_GET['edit'])){
  $edit_id = (int)$_GET['edit'];
  $inventoryResults = $db->query("SELECT * FROM inventory WHERE id ='$edit_id'");
  $inventory = mysqli_fetch_assoc($inventoryResults);

  $title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):$inventory['title']);
  $threshold = ((isset($_POST['threshold']) && $_POST['threshold'] != '')?sanitize($_POST['threshold']):$inventory['threshold']);
  $unit = ((isset($_POST['unit']) && $_POST['unit'] != '')?sanitize($_POST['unit']):$inventory['unit']);
  $description = ((isset($_POST['description']))?sanitize($_POST['description']):$inventory['description']);
  $quantity = ((isset($_POST['quantity']) && $_POST['quantity'] != '')?sanitize($_POST['quantity']):0);
  $price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):0);
  $qupdate = $inventory['quantity'];
  $pupdate = $inventory['price'];
  $inventory_consumption = $inventory['quantity'];
}
$inventory_consumption = $inventory_consumption + $quantity;
$qupdate = $qupdate + $quantity;
$pupdate = $pupdate + $price;


if($_POST) {

  $required = array('title', 'price', 'quantity', 'threshold', 'unit');

  foreach ($required as $field) {
    if($_POST[$field] == ''){
      //echo $field;
      $errors[] = 'All fields with an astrisk(*) are required.';
      break;
    }
  }


  if(!empty($errors)){
    echo display_errors($errors);
  }
  else{

    $insertSql = "INSERT INTO inventory (`title`,`price`,`quantity`,`threshold`,`unit`,`description`,`inventory_consumption`)
     VALUES ('$title','$pupdate','$qupdate','$threshold','$unit','$description','$inventory_consumption')";
     if(isset($_GET['edit'])){
       $insertSql = "UPDATE inventory SET `title` = '$title', `price` = '$pupdate',
      `quantity` = '$qupdate', `threshold` = '$threshold', `description` = '$description', `inventory_consumption` = '$inventory_consumption',
       `unit` = '$unit' WHERE id ='$edit_id'";
     }

    $db->query($insertSql);
    header('Location: inventory.php');
  }
}
?>
  <h2 class="text-center"><?php echo((isset($_GET['edit']))?'Edit':'Add A New');?> inventory</h2><hr>
  <form action="inventory.php?<?php echo((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="POST" enctype="multipart/form-data">
    <div class="form-group col-md-3">
      <label for="title">Title*:</label>
      <input type="text" name="title" class="form-control" id="title" value="<?php echo $title;?>">
    </div>

    <div class="form-group col-md-3">
      <label for="price">Price paid*:</label>
      <input type="text" id="price" name="price" class="form-control" placeholder="0.00">
    </div>
    <div class="form-group col-md-3">
      <label for="quantity">Quantity added*:</label>
      <input type="text" id="quantity" name="quantity" class="form-control" placeholder="0.00">
    </div>
    <div class="form-group col-md-2">
      <label for="threshold">Threshold*:</label>
      <input type="number" id="threshold" name="threshold" class="form-control" value="<?php echo $threshold;?>" >
    </div>
    <div class="form-group col-md-1">
      <label for="unit">Unit*:</label>
      <select class="form-control" name="unit">
        <option value=""<?php echo(($unit =='')?' selected':'');?>></option>
          <option value="units"<?php echo(($unit =='units')?' selected':'');?>>Units/Unit</option>
          <option value="Kg"<?php echo(($unit =='Kg')?' selected':'');?>>Kg</option>
          <option value="Ltr."<?php echo(($unit =='Ltr.')?' selected':'');?>>Ltr.</option>
      </select>
    </div>
    <div class="row" style="margin-left: -1px;">
    <div class="form-group col-md-6">
      <label for="description">Description:</label>
      <textarea id="description" name="description" class="form-control" rows=6><?php echo $description;?></textarea>
    </div>
    <div class="form-group col-md-6"></div>
  </div>
    <div class="form-group pull-right">
      <a href="inventory.php" class="btn btn-default">Cancel</a>
      <input type="submit" value="<?php echo((isset($_GET['edit']))?'Edit ':'Add ')?>inventory" class="btn btn-success">
    </div><div class="clearfix"></div>
  </form>

<?php
 }
else{

$sql = "SELECT * FROM inventory WHERE deleted = 0";
$presults = $db->query($sql);
?>
<h2 class="text-center">Inventory</h2>
<a href="inventory.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Inventory</a>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead class="text-center"><?php if((has_permission())):?><th></th><?php endif;?><th>Weigh Inventory</th><th>Inventory</th><th>Quantity</th><th>Amount(till date)</th><th>(till date)</th></thead>
  <tbody>
    <?php while($inventory = mysqli_fetch_assoc($presults)):
       ?>
      <tr>
        <?php if((has_permission())):?>
        <td>
          <a href="inventory.php?edit=<?php echo $inventory['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
          <a href="inventory.php?delete=<?php echo $inventory['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
      <?php endif;?>
        <td>
          <a href="inventory.php?cons=<?php echo $inventory['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-scale"></span></a>
        </td>
        <td><?php echo $inventory['title'];?></td>
        <td><?php echo ($inventory['quantity'].' '.$inventory['unit']);?></td>
        <td><?php echo (money($inventory['price'])); ?></td>
        <td><?php echo($inventory['inventory_consumption'].' '.$inventory['unit']);?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php } include 'includes/footer.php'; ?>
