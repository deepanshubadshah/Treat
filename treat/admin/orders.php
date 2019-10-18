<?php
  require_once '../core/init.php';
  if(!is_logged_in()){
    header('Location login.php');
  }
  include 'includes/head.php';
  include 'includes/navigation.php';

  //complete order
  if(isset($_GET['complete']) && $_GET['complete']==1){
    $cart_id = sanitize((int)$_GET['cart_id']);
    $db->query("UPDATE cart SET shipped = 1 WHERE id = '{$cart_id}'");
    $_SESSION['success_flash'] = "The Order Has Been Completed!";
    header('Location: index.php');
   }

  /*
  if(isset($_GET['report']) && $_GET['report']==1){
      $user_id = sanitize((int)$_GET['user_id']);
      $db->query("SELECT reports FROM customer WHERE id= '{$}'")
      $db->query("UPDATE cart SET ");
  }*/

  $txn_id = sanitize((int)$_GET['txn_id']);
  $txnQuery= $db->query("SELECT * FROM Orders WHERE id = '{$txn_id}'");
  $txn = mysqli_fetch_assoc($txnQuery);
  $cart_id = $txn['cart_id'];
  $cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
  $cart = mysqli_fetch_assoc($cartQ);
  $items = json_decode($cart['items'],true);
  $idArray = array();
  $products = array();
  foreach ($items as $item) {
    $idArray[] = $item['id'];
  }
  $ids = implode(',',$idArray);
  $productQ = $db->query("SELECT i.id as 'id', i.title as 'title', i.price as 'price', c.id as 'cid', c.category as 'category'
    FROM Products i
    LEFT JOIN pro_cat c ON i.category = c.id

    WHERE i.id IN ({$ids})
  ");
  while($p = mysqli_fetch_assoc($productQ)){
    foreach($items as $item){
      if($item['id'] == $p['id']){
        $x = $item;
        continue;
      }
    }
    $products[] = array_merge($x,$p);
  }
?>
<h2 class="text-center">Items Ordered</h2>
<div >
  <a href="index.php" class="btn btn-large btn-default" style="margin-right:20px;"><span>&nbsp;Back&nbsp;</span></a>
</div>
<br>
<table class="table table-condensed table-bordered table-striped">
  <thead><th>#</th><th>Title</th><th>Price</th><th>Quantity</th><th>Sub total</th></thead>
  <tbody>
    <?php $n=1; foreach($products as $product): ?>
      <tr>
        <td><?php echo($n);?></td>
        <td><?php echo($product['title']);?></td>
        <td><?php echo(money($product['price']));?></td>
        <td><?php echo($product['quantity']);?></td>
        <td><?php echo(($product['price']*$product['quantity']));?></td>
      </tr>
    <?php $n++; endforeach;?>
  </tbody>
</table>

<div class="row">
  <div class="col-md-6">
    <h3 class="text-center">Order Details</h3>
    <table class="table table-condensed table-striped table-bordered">
      <tbody>
        <tr>
          <td>Sub Total</td>
          <td><?php echo(money($txn['sub_total']));?></td>
        </tr>
        <tr>
          <td>Tax</td>
          <td><?php echo(money($txn['tax']));?></td>
        </tr>
        <tr>
          <td>Grand Total</td>
          <td><?php echo(money($txn['grand_total']));?></td>
        </tr>
        <tr>
          <td>Order Date</td>
          <td><?php echo(pretty_date($txn['txn_date']));?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
