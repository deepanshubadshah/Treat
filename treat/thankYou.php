<?php
require_once 'core/init.php';

//$customer_id = sanitize();
$full_name = sanitize($_POST['full_name']);
$mob_no = sanitize($_POST['mob_no']);
$street = sanitize($_POST['street']);
$street2 = sanitize($_POST['street2']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip_code = sanitize($_POST['zip_code']);
//$country = sanitize($_POST['country']);
$payment_type = sanitize($_POST['payment_type']);
$tax = sanitize($_POST['tax']);
$sub_total = sanitize($_POST['sub_total']);
$grand_total = sanitize($_POST['grand_total']);
$savings = sanitize($_POST['savings']);
$cart_id = sanitize($_POST['cart_id']);
$description = sanitize($_POST['description']);
$charge_amount = number_format($grand_total,2) * 100;
$metadata = array(
  "card_id"   => $cart_id,
  "tax"       => $tax,
  "sub_total" => $sub_total,
);

$itemQ = $db->query("SELECT * FROM cart WHERE id='{$cart_id}'");
$iresults = mysqli_fetch_assoc($itemQ);
$items = json_decode($iresults['items'],true);
foreach ($items as $item) {
  $newSizes = array();
  $item_id = $item['id'];
  $productQ = $db->query("SELECT sizes FROM Products WHERE id = '{$item_id}'");
  $product = mysqli_fetch_assoc($productQ);
  $sizes = sizesToArray($product['sizes']);
  foreach ($sizes as $size) {
    if($size['size'] == $item['size']){
      $q = $size['quantity'] - $item['quantity'];
      $newSizes[] = array('size' => $size['size'],'quantity' => $q);
    }
    else{
      $newSizes[] = array('size' => $size['size'],'quantity' => $size['quantity']);
    }
  }
  $sizeString = sizesToString($newSizes);
  $db->query("UPDATE Products SET sizes = '{$sizeString}' WHERE id = '{$item_id}'");
}

if($payment_type != 'cod')
{
$db->query("UPDATE cart SET paid = 1 WHERE id ='{$cart_id}'");
}

$db->query("INSERT INTO Orders
  (`cart_id`,`full_name`,`mob_no`,`street`,`street2`,`city`,`state`,`zip_code`,`country`,`sub_total`,`tax`,`grand_total`,`description`,`payment_type`) VALUES
  ('$cart_id','$full_name','$mob_no','$street','$street2','$city','$state','$zip_code','India','$sub_total','$tax','$grand_total','$description','$payment_type')");

$domain = ($_SERVER['HTTP_HOST'] != 'localhost')?'.'.$_SERVER['HTTP_HOST']:false;
setcookie(CART_COOKIE,'',1,"/",$domain,false);
include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';
?>

  <h1 class="text-center text-success">Thank You!</h1>
  <p>&nbsp; Your order has been successfully placed. You have to pay <strong><?php echo(money($grand_total));?></strong> during delivery.<p>
  <p>&nbsp; Your order number is: <strong><?php echo($cart_id);?></strong></p><br>
  <p>&nbsp; Your order will be shipped to the address below.</p>
  <address>
    <?php echo('&nbsp;'.$full_name);?><br>
    <?php echo('&nbsp;'.$street);?><br>
    <?php echo(($street2 != '')?'&nbsp;'.$street2.'<br>':'');?>
    <?php echo('&nbsp;'. $city.', '.$state.'<br>'. $zip_code);?><br>
  </address>
<?php
include 'includes/footer.php';

?>
<!-- your card has been successfully charged money($grand_total). You have been emailed a receipt. Please
check your spam folder if it is not in your inbox. Additionally you can print this page as a receipt
<p>Your receipt number is: <strong>$cart_id</strong></p>
-->
