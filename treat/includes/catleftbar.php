<?php
 require_once 'core/init.php';
 $sql = "SELECT * FROM Products WHERE featured = 1 AND deleted = 0 AND categories = '$cat_id'";
 $productQ = $db->query($sql);
 $category = get_category($cat_id);
 ?>
<h2 class="text-center"><?php echo($category['parent'].' '.$category['child']);?></h2>
<div class="col-md-2">Left Side Bar</div>
