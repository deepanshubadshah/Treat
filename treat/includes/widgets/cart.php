<h3 class="text-center">Table order</h3>
<div>
  <?php if(empty($cart_id)): ?>
    <p class="text-center">No table order till yet!</p>
  <?php else:
      $cartQu = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
      $results = mysqli_fetch_assoc($cartQu);
      $items = json_decode($results['items'],true);
      $i = 1;
      $sub_total = 0;
  ?>
<table class="table table-condensed" id="cart_widget">
  <tbody>
    <?php foreach ($items as $item): {
      $productQ = $db->query("SELECT * FROM Products WHERE id = '{$item['id']}'");
      $product = mysqli_fetch_assoc($productQ);
    }
    ?>
    <tr>
      <td><?php echo($item['quantity']);?></td>
      <td><?php echo(substr($product['title'],0,10));?></td>
      <td><?php echo(money($item['quantity'] * $product['price']));?></td>
    </tr>
  <?php
    $sub_total = $sub_total + ($item['quantity'] * $product['price']);
  endforeach;?>
  <tr>
    <td></td>
    <td>Sub Total</td>
    <td><?php echo(money($sub_total));?></td>
  </tr>
  </tbody>
</table>
<a href="cart.php" class="btn btn-xs btn-primary pull-right">View Order</a>
<div class="clearfix"></div>
  <?php endif;?>
</div>
