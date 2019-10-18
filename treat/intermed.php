<?php
require_once 'core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';

$cart_id = sanitize($_POST['cart_id']);
$tax = $_POST['tax'];
$sub_total = sanitize($_POST['sub_total']);
$grand_total = sanitize($_POST['grand_total']);
$payment_type = sanitize($_POST['payment_type']);
$description = sanitize($_POST['description']);

$coworker = sanitize($person_data['person_name']);
$ccount=0;

$itemQ = $db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
$iresults = mysqli_fetch_assoc($itemQ);
$items = json_decode($iresults['items'],true);
foreach ($items as $item) {
  $newSizes = array();
  $item_id = $item['id'];
  $productQ = $db->query("SELECT * FROM Products WHERE id = '{$item_id}'");
  $product = mysqli_fetch_assoc($productQ);
  $sold = $product['sold'] + $item['quantity'];
  $quantity = $product['quantity'] - $item['quantity'];
  $db->query("UPDATE Products SET `quantity` = '$quantity', `sold` = '$sold'  WHERE id = '{$item_id}'");
}


{
  $db->query("INSERT INTO Orders
    (`cart_id`,`sub_total`,`tax`,`grand_total`,`description`,`payment_type`,`coworker`) VALUES
    ('$cart_id','$sub_total','$tax','$grand_total','$description','$payment_type','$coworker')");
    $ccount=1;
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false;
    setcookie(CART_COOKIE,'',1,"/",$domain,false);
}
?>
<form action="printing.php" method="post" id="printform" enctype="multipart/form-data">
  <input type="hidden" name="sub_total" value="<?php echo($sub_total);?>">
  <input type="hidden" name="grand_total" value="<?php echo($grand_total);?>">
  <input type="hidden" name="cart_id" value="<?php echo($cart_id);?>">
  <div class="form-group">
  <input type="submit" value="Proceed" class="btn btn-primary">
  </div>
</form>
<?php
if($ccount==1)
{?>

<script type="text/javascript">
    document.getElementById('printform').submit(); // SUBMIT FORM
</script>

<?php
}?>
