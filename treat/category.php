<?php
 require_once 'core/init.php';
 include 'includes/head.php';
 include 'includes/navigation.php';
 include 'includes/headerpartial.php';

 if(isset($_GET['cat'])){
   $cat_id = sanitize($_GET['cat']);
 }
 else {
   $cat_id = '';
 }

	$sql = "SELECT * FROM Products WHERE featured = 1 AND deleted = 0 AND categories = '$cat_id'";
  $productQ = $db->query($sql);
  $category = get_category($cat_id);

 ?>

<h2 class="text-center"><?php echo($category['parent'].' '.$category['child']);?></h2>
 <?php
 include 'includes/leftbar.php';
?>

<!-- main content -->
  <!--  <div class="col-md-2">Left Side Bar</div>  -->
		<div class="col-md-8">
		<div class="row">

		<?php while($product = mysqli_fetch_assoc($productQ)) : ?>
			<div class="col-md-3 text-center">
				<h4><?php echo $product['title']; ?></h4>
        <?php $photos = explode(',',$product['image']); ?>
				<img src="<?php echo $photos[0]; ?>" alt="<?php echo $product['title']; ?>" class="img-thumb" />
				<p class="list-price text-danger">List Price: <s>Rs <?php echo $product['list_price'];?></s></p>
				<p class="Price">Our Price: Rs <?php echo $product['price']; ?></p>
				<button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?php echo $product['id']; ?>)">Details</button>
        <!--<footer id="footer"></footer>-->
			</div>
		<?php endwhile ; ?>
		</div>
		</div>

<?php
	include 'includes/rightbar.php';
  include 'includes/footer.php';
?>
