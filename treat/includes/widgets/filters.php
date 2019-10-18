<?php
  $cat_id = ((isset($_REQUEST['cat']))?sanitize($_REQUEST['cat']):'');
  $price_sort = ((isset($_REQUEST['price_sort']))?sanitize($_REQUEST['price_sort']):'');
  $min_price = ((isset($_REQUEST['min_price']))?sanitize((($_REQUEST['min_price']) >= 0 && ($_REQUEST['min_price'])<100000000)?($_REQUEST['min_price']):''):'');
  $max_price = ((isset($_REQUEST['max_price']))?sanitize((($_REQUEST['max_price']) >= 0 && ($_REQUEST['max_price'])<100000000)?($_REQUEST['max_price']):''):'');
  $b = ((isset($_REQUEST['brand']))?sanitize($_REQUEST['brand']):'');
  $brandQ = $db->query("SELECT * FROM Brand ORDER BY brand");
  //SELECT * from tutorials_tbl \   WHERE BINARY tutorial_author = 'sanjay';
?>
<h3 class="text-center">Search By:</h3>
<h4 class="text-center">Price</h4>
<form action="search.php" method="post">
  <input type="hidden" name="cat" value="<?php echo($cat_id);?>">
  <input type="hidden" name="price_sort" value="0">
  <input type="radio" name="price_sort" value="low"<?php echo(($price_sort == 'low')?' checked':'');?>>Low to High<br>
  <input type="radio" name="price_sort" value="high"<?php echo(($price_sort == 'high')?' checked':'');?>>High to Low<br><br>
  <input type="text" style="width: 30%;padding: 1.3px 1px;box-sizing: border-box;" name="min_price" class="price-range border" placeholder="Rs Min" min="0" max="100000000" maxlength="9" value="<?php echo($min_price);?>"> To
  <input type="text" style="width: 30%;padding: 1.3px 1px;box-sizing: border-box;" name="max_price" class="price-range  form-control-sm" placeholder="Rs Max" min="((<?php echo($min_price);?>) !='')?<?php echo($min_price);?>:50" max="1000000000" maxlength="10" maxlength="10" value="<?php echo($max_price);?>"><br><br>
  <h4 class="text-center">Brand</h4>
  <input type="radio" name="brand" value=""<?php echo(($b == '')?' checked':'');?>>ALL<br>
  <?php while($brand = mysqli_fetch_assoc($brandQ)): ?>
    <input type="radio" name="brand" value="<?php echo($brand['id']);?>"<?php echo(($b == $brand['id'])?' checked':'');?>><?php echo($brand['brand']);?><br>
  <?php endwhile;?>
  <input type="submit" value="Search" class="btn btn-xs btn-primary">
</from>
