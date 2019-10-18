<?php
require_once '../core/init.php';
$id = $_POST['id'];
$id = (int)$id;
$sql = "SELECT * FROM Products WHERE id = '$id'";
$result = $db->query($sql);
$product = mysqli_fetch_assoc($result);
$brand_id = $product['brand'];
$sql = "SELECT brand FROM Brand WHERE id='$brand_id'";
$brand_query = $db->query($sql);
$brand = mysqli_fetch_assoc($brand_query);
$sizevalues = $product['sizes'];
$size_arr = explode(',', $sizevalues);
?>


<!--Details Modal-->
<?php ob_start(); ?>
<div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
  <div class="modal-content">
      <div class="modal-header">
        <button class="close" type="button" onclick="closeModal()" aria-label="close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center"><?php echo $product['title'];?></h4>

      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <span id="modal_errors" class="bg-danger"></span>
            <div class="col-sm-6 fotorama" data-keyboard="true" data-allowfullscreen="true" >
              <?php $photos = explode(',',$product['image']);
              foreach ($photos as $photo): ?>
                <img src="<?php echo $photo; ?>" alt="<?php echo $product['title'];?>" class="details  img-responsive">
              <?php endforeach; ?>
            </div>
            <div class="col-sm-6">
              <h4>Details</h4>
              <p><?php echo nl2br($product['description']); ?>
              <hr>
              <p>Price: Rs <?php echo $product['price'];?></p>
              <p>Brand: <?php echo $brand['brand'];?></p>
              <form action="add_cart.php" method="post" id="add_product_form">
                <input type="hidden" name="product_id" value="<?php echo($id);?>">
                <input type="hidden" name="available" id="available" value="">
                <div class="form-group">
                  <div class="col-xs-3">
                    <label for="quantity">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" max="1000" maxlength="3">
                  </div>

                  <div class="col-xs-9"></div>
                  <!--<p>Available: 3</p> -->
                </div>
                <br><br>
                <div class="form-group">
                </br>
              </br>
                  <label for="size">Size:</label>
                  <select name="size" id="size" class="form-control">
                    <option value="">Select size</option>
                    <?php foreach($size_arr as $string){
                      $size_valarr = explode(':',$string);
                      $size = $size_valarr[0];
                      $available = $size_valarr[1];
                      if($available > 0){
                      echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' (',$available.' Available)</option>';
                    }
                    }?>
                  </select>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" onclick="closeModal()">Close</button>
        <button class="btn btn-warning" onclick="add_to_cart();return false;"><span class="glyphicon glyphicon-shopping-cart"></span>Add To Cart</button>
      </div>
    </div>
  </div>
</div>
<script>
 jQuery('#size').change(function(){
   var available = jQuery('#size option:selected').data("available");
   jQuery('#available').val(available);
 });

 $(function () {
  $('.fotorama').fotorama({'loop':true,'autoplay':true});
});

  function closeModal(){
    jQuery('#details-modal').modal('hide');
    setTimeout(function(){
      jQuery('#details-modal').remove();
      jQuery('.modal-backdrop').remove();
    },500);
  }
</script>
<?php echo ob_get_clean();?>
