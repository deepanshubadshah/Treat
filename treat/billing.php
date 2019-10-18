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
  ?>

<div>
<h2 class="text-center">Featured Products</h2><br>
<a href="cart.php" class="btn btn-success pull-right" style="margin-right:10px" id="add-product-btn">Next>></a>
<div class="clearfix"></div>
<hr>
<br>
 <?php
 $csql = "SELECT * FROM pro_cat ORDER BY category";
 $pro_cate = $db->query($csql);

	$sql = "SELECT * FROM Products WHERE featured = 1 AND deleted = 0 ORDER BY title";
	$feature = $db->query($sql);
?>
<style>
.img-thumb {
    width: 110px;
    height: 100px;
}
</style>

<!-- main content -->
    <!--<div class="col-md-2">Left Side Bar</div> -->
		<div class="col-md-10">
		<div class="row">
		<?php while($product = mysqli_fetch_assoc($feature)) : ?>
			<div class="col-md-3 text-center" >
        <div style="border-style: solid; border-width:thin; border-color: #f5f4f4;margin-bottom:10px; border-radius: 5px;">
        <div class="row" >
          <div class="col-md-4">
            <?php $photos = explode(',',$product['image']); ?>
    				<img src="<?php echo $photos[0]; ?>" alt="<?php echo $product['title']; ?>" class="img-thumb" style="border-style: solid; border-width:thin; border-color: #f5f4f4; border-radius: 5px;">
          </div>
          <div class="col-md-8">
            <h4 style="font-size: 15px;"><?php echo $product['title']; ?></h4>
    				<p class="Price">Rs <?php echo $product['price']; ?></p>
    				<button type="button" class="btn btn-sm btn-success" style="padding: 5px 11px;" onclick="add_to_cart('<?php echo($product['id']);?>','1');">&nbsp;Add&nbsp;</button>
          </div>
        <!--<footer id="footer"></footer>-->
      </div>
      </div>
			</div>
		<?php endwhile ; ?>
		</div>
    <br>

    <?php while($categories = mysqli_fetch_assoc($pro_cate)):
      $cate_id = $categories['id'];
      $msql = "SELECT * FROM Products WHERE category = '$cate_id' AND deleted = 0";
    	$catprod = $db->query($msql);
      ?>
      <h2 style="margin-left:20px;"><?php echo($categories['category']);?></h2>
      <div class="row">
  		<?php while($cproduct = mysqli_fetch_assoc($catprod)) : ?>
  			<div class="col-md-3 text-center" >
          <div style="border-style: solid; border-width:thin; border-color: #f5f4f4;margin-bottom:10px; border-radius: 5px;">
          <div class="row" >
            <div class="col-md-4">
              <?php $photos = explode(',',$cproduct['image']); ?>
      				<img src="<?php echo $photos[0]; ?>" alt="<?php echo $cproduct['title']; ?>" class="img-thumb" style="border-style: solid; border-width:thin; border-color: #f5f4f4; border-radius: 5px;">
            </div>
            <div class="col-md-8">
              <h4 style="font-size: 15px;"><?php echo $cproduct['title']; ?></h4>
      				<p class="Price">Rs <?php echo $cproduct['price']; ?></p>
      				<button type="button" class="btn btn-sm btn-success" style="padding: 5px 11px;" onclick="add_to_cart('<?php echo($cproduct['id']);?>','1');">&nbsp;Add&nbsp;</button>
            </div>
          <!--<footer id="footer"></footer>-->
        </div>
        </div>
  			</div>
  		<?php endwhile ; ?>
  		</div>
        <br>
    <?php endwhile;?>

		</div>

<!--<div class="col-md-2">Right Side Bar</div> -->
<?php
	include 'includes/rightbar.php';
  ?>
</div>
<?php  include 'includes/footer.php';
?>
