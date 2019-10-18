<?php
require_once 'core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
//include 'includes/headerpartial.php';

if($cart_id != '' ){
  $cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
  $result = mysqli_fetch_assoc($cartQ);
  $items = json_decode($result['items'],true);
  $i = 1;
  $sub_total = 0;
  $item_count = 0;
}
?>

<div class="col-md-12">
  <div class="row">
    <h2 class="text-center">Table order</h2><hr>
    <?php if($cart_id == ''):?>
      <div class="bg-danger">
        <p class="text-center text-danger">
          No table order yet!
        </p>
      </div>
    <?php else: ?>
      <table class="table table-bordered table-condensed table-striped">
        <thead><th>#</th><th>Item</th><th>Price</th><th>Quantity</th><th>Sub Total</th></thead>
        <tbody>
          <?php
            foreach ($items as $item) {
              $product_id = $item['id'];
              $productQ = $db->query("SELECT * FROM Products WHERE id = '{$product_id}'");
              $product = mysqli_fetch_assoc($productQ);
              ?>
              <tr>
                <td><?php echo($i);?></td>
                <td><?php echo($product['title']);?></td>
                <td><?php echo(money($product['price']));?></td>
                <td>
                  <button class="btn btn-xs btn-default" onclick="update_cart('removeone','<?php echo($product['id']);?>');">-</button>
                  <?php echo($item['quantity']);?>
                  <?php if($item['quantity'] < $product['quantity']): ?>
                    <button class="btn btn-xs btn-default" onclick="update_cart('addone','<?php echo($product['id']);?>');">+</button>
                  <?php else: ?>
                    <span class="text-danger">Max</span>
                  <?php endif; ?>
                </td>
                <td><?php echo(money($item['quantity'] * $product['price']));?></td>
                <?php $i= $i +1; ?>
              </tr>
            <?php
            $item_count = $item_count + $item['quantity'];
            $sub_total = $sub_total + ($item['quantity'] * $product['price']);
          }

          $tax = TAXRATE * $sub_total;
          $tax = number_format($tax,2);
          $grand_total = ($sub_total);
          ?>
        </tbody>
        </table>
        <table class="table table-bordered table-condensed text-right">
          <legend align= center >Total</legend>
          <thead class="totals-table-header"><th>Total Items</th><th>Sub Total</th><th>Tax</th><th>Grand Total</th></thead>
          <tbody>
            <tr>
              <td><?php echo($item_count);?></td>
              <td><?php echo(money($sub_total));?></td>
              <td><?php echo(money($tax)); ?></td>
              <td class="bg-success"><?php echo(money($grand_total)); ?></td>
            </tr>
          </tbody>
        </table>

<!-- Check out button -->
<button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#checkoutModal ">
<span class="glyphicon glyphicon-shopping-cart"></span>  Check Out >>
</button>

<!-- Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="checkoutModalLabel">Payment method</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <form action="intermed.php" method="post" id="payment-form">
            <span class="bg-danger" id="payment-errors"></span>
            <input type="hidden" name="tax" value="<?php echo($tax);?>">
            <input type="hidden" name="sub_total" value="<?php echo($sub_total);?>">
            <input type="hidden" name="grand_total" value="<?php echo($grand_total);?>">
            <input type="hidden" name="cart_id" value="<?php echo($cart_id);?>">
            <input type="hidden" name="payment_type" value="<?php echo($payment_type);?>">
            <input type="hidden" name="description" value="<?php echo($item_count.' item'.(($item_count>1)?'s':'').' from Treat.');?>">
            <div>
              <div class="form-check col-md-3">
                <input class="form-check-input" type="radio" name="payment_type" id="cod" value="cash" >
                <label class="code" for="cash" style="width: 28px; height:auto"><span><img src="/treat/images/sz/cashod23.png"></span></label>
              </div>
              <div class="form-check col-md-3">
                <input class="form-check-input" type="radio" name="payment_type" id="paytm" value="paytm" >
                <label class="code" for="paytm" style="width: 28px; height:auto"><span><img src="/treat/images/sz/Paytm2b.png"></span></label>
              </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="checkout_button">Check Out</button>
        </form>
      </div>
    </div>
  </div>
</div>

    <?php endif;?>
  </div>
</div>

<script>

</script>

<?php include 'includes/footer.php'?>
