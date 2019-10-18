<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/treat/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

$msql1 = "SELECT * FROM pro_cat ";
$mquery1 = $db->query($msql1);

//Delete products
if(isset($_GET['delete'])){
  $id = sanitize($_GET['delete']);
  $db->query("UPDATE Products SET deleted = 1 WHERE id = '$id'");
  header('Location: products.php');
}

$dbpath='';
if (isset($_GET['add']) || isset($_GET['edit'])){
$categoryQuery = $db->query("SELECT * FROM pro_cat ORDER BY category");

$title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):'');
$category = ((isset($_POST['category']) && !empty($_POST['category']))?sanitize($_POST['category']):'');
$price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):'');
$description = ((isset($_POST['description']) && $_POST['description'] != '')?sanitize($_POST['description']):'');
$quantity = ((isset($_POST['quantity']) && $_POST['quantity'] != '')?sanitize($_POST['quantity']):'');
$threshold = ((isset($_POST['threshold']) && $_POST['threshold'] != '')?sanitize($_POST['threshold']):'');
$saved_image = '';

if(isset($_GET['edit'])){
  $edit_id = (int)$_GET['edit'];
  $productResults = $db->query("SELECT * FROM Products WHERE id ='$edit_id'");
  $product = mysqli_fetch_assoc($productResults);

  if(isset($_GET['delete_image'])){
    $imgi = (int)$_GET['imgi'] - 1;
    $images = explode(',',$product['image']);
    $image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgi];
    unlink($image_url);
    unset($images[$imgi]);
    $imagesString = implode(',',$images);
    $db->query("UPDATE Products SET image = '{$imagesString}' WHERE id = '$edit_id'");
    header('Location: products.php?edit='.$edit_id);
  }

  $title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):$product['title']);
  $category = ((isset($_POST['category']) && $_POST['category'] != '')?sanitize($_POST['category']):$product['category']);
  $price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):$product['price']);
  $description = ((isset($_POST['description']))?sanitize($_POST['description']):$product['description']);
  $quantity = ((isset($_POST['quantity']) && $_POST['quantity'] != '')?sanitize($_POST['quantity']):$product['quantity']);
  $threshold = ((isset($_POST['threshold']) && $_POST['threshold'] != '')?sanitize($_POST['threshold']):$product['threshold']);
  $saved_image = (($product['image'] != '')?$product['image']:'');
  $dbpath = $saved_image;
}

if ($_POST) {
//  $sizes = sanitize(rtrim($_POST['sizes'],', '));
  $dbpath = '';
  $errors = array();
  $required = array('title', 'category', 'price', 'quantity');
  $allowed = array('png','jpg','jpeg','gif');

  $uploadPath = array();
  $uploadName = array();
  $tmpLoc = array();
  foreach ($required as $field) {
    if($_POST[$field] == ''){
      //echo $field;
      $errors[] = 'All fields with an astrisk(*) are required.';
      break;
    }
  }

  $photoCount = count($_FILES['photo']['name']);

  /*if($photoCount > 10){
    $errors[] = 'You can upload maximum 10 images';
  }*/

  if(empty($_FILES)) {
    $dbpath = $product['image'];
  }
  if($photoCount > 0) {
    for($i = 0;$i<$photoCount;$i++){
      $name = $_FILES['photo']['name'][$i];
      $fileExt=sanitize('');
      $mimeExt=sanitize('');
      /*$photo = $_FILES['photo'];
      $name = $photo['name'];*/
      $nameArray = explode('.',$name);
      $fileName = $nameArray[0];
      if($nameArray[0]!=''){
      $fileExt = sanitize($nameArray[1]);
      }
      $mime = explode('/',$_FILES['photo']['type'][$i]);
      $mimeType = $mime[0];
      if($mime[0]!=''){
      $mimeExt = sanitize($mime[1]);
      }

      $tmpLoc[] = $_FILES['photo']['tmp_name'][$i];
      $fileSize = $_FILES['photo']['size'][$i];
      $uploadName = md5(microtime().$i).'.'.$fileExt;
      $uploadPath[] = BASEURL.'images/sz/prod/'.$uploadName;
      if($i != 0 && $i < $photoCount){
        $dbpath .= ',';
      }
      $dbpath .= '/treat/images/sz/prod/'.$uploadName;
      if ($mimeType != 'image') {
        $errors[] = 'The file must be an image.';
      }
      if(!in_array($fileExt, $allowed)){
        $errors[] = 'The file extension must be a png, jpg, jpeg or gif.';
      }
      if ($fileSize > 15000000){
        $errors[] = 'The file size must be under 15MB.';
      }
      if($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')){
        $errors[] = 'File extension does not match the file. ' ;
      }
    }
  }


  if(!empty($errors)){
    echo display_errors($errors);
  }
  else{
    //upload file and insert into database
    if($photoCount > 0){
      for($i=0;$i<$photoCount;$i++){
          move_uploaded_file($tmpLoc[$i],$uploadPath[$i]);
      }}

    $insertSql = "INSERT INTO Products (`title`,`price`,`category`,`quantity`,`threshold`,`image`,`description`)
     VALUES ('$title','$price','$category','$quantity','$threshold','$dbpath','$description')";
     if(isset($_GET['edit'])){
       $insertSql = "UPDATE Products SET `title` = '$title', `price` = '$price', `category` = '$category',
        `quantity` = '$quantity', `threshold` = '$threshold', `image` = '$dbpath', `description` = '$description' WHERE id ='$edit_id'";
     }

    $db->query($insertSql);
    header('Location: products.php');
  }
}
?>
  <h2 class="text-center"><?php echo((isset($_GET['edit']))?'Edit':'Add A New');?> Product</h2><hr>
  <form action="products.php?<?php echo((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="POST" enctype="multipart/form-data">
    <div class="form-group col-md-3">
      <label for="title">Title*:</label>
      <input type="text" name="title" class="form-control" id="title" value="<?php echo $title;?>">
    </div>
    <div class="form-group col-md-3">
      <label for="category">Category*:</label>
      <select class="form-control" id="category" name="category">
        <option value=""<?php echo (($category == '')?' selected':'');?>></option>
        <?php while($b = mysqli_fetch_assoc($categoryQuery)): ?>
          <option value="<?php echo $b['id'];?>"<?php echo (($category == $b['id'])?' selected':'');?>><?php echo $b['category'];?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group col-md-3">
      <label for="price">Price*:</label>
      <input type="text" id="price" name="price" class="form-control" value="<?php echo $price;?>">
    </div>

    <div class="form-group col-md-3">
      <label for="quantity">Quantity*:</label>
      <input type="text" class="form-control" name="quantity" id="quantity" value="<?php echo $quantity;?>">
    </div>
    <div class="form-group col-md-3">
      <label for="threshold">Threshold:</label>
      <input type="text" class="form-control" name="threshold" id="threshold" value="<?php echo $threshold;?>">
    </div>
    <div class="form-group col-md-6">
      <?php if($saved_image != ''): ?>
        <?php
          $imgi = 1;
          $images = explode(',',$saved_image);
        ?>
        <?php foreach ($images as $image):?>
        <div class="saved-image col-md-4">
          <img src="<?php echo($image);?>" alt="saved-image" class="img-thumb"/><br>
          <a href="products.php?delete_image=1&edit=<?php echo($edit_id);?>&imgi=<?php echo($imgi);?>" class="text-danger">Delete Image</a>
        </div>
      <?php $imgi++;
     endforeach;?>
      <?php else:?>
        <label for="photo">Product Photo:</label>
        <input type="file" name="photo[]" id="photo" class="form-control" multiple >
      <?php endif;?>
    </div>
    <div class="form-group col-md-6">
      <label for="description">Description:</label>
      <textarea id="description" name="description" class="form-control" rows=6><?php echo $description;?></textarea>
    </div>
    <div class="form-group pull-right">
      <a href="products.php" class="btn btn-default">Cancel</a>
      <input type="submit" value="<?php echo((isset($_GET['edit']))?'Edit ':'Add ')?>Product" class="btn btn-success">
    </div><div class="clearfix"></div>
  </form>

<?php
 }
else{

$sql = "SELECT * FROM Products WHERE deleted = 0";
$presults = $db->query($sql);
if(isset($_GET['featured'])){
  $id = (int)$_GET['id'];
  $featured = (int)$_GET['featured'];
  $featuredsql = "UPDATE Products SET featured = '$featured' WHERE id = '$id'";
  $db->query($featuredsql);
  header('Location: products.php');
}
?>
<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead class="text-center"><th></th><th>Product</th><th>Price</th><th>Category</th><th>Featured</th><th>Sold(till date)</th></thead>
  <tbody>
    <?php while($product = mysqli_fetch_assoc($presults)):
      $cate_id = $product['category'];
      $categ = $db->query("SELECT * FROM pro_cat WHERE id = '$cate_id'");
      $categor = mysqli_fetch_assoc($categ);
       ?>
      <tr>
        <td>
          <a href="products.php?edit=<?php echo $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
          <a href="products.php?delete=<?php echo $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
        <td><?php echo $product['title'];?></td>
        <td><?php echo money($product['price']);?></td>
        <td><?php echo $categor['category'] ;?></td>
        <td><a href="products.php?featured=<?php echo (($product['featured']==0)?'1':'0');?>&id=<?php echo $product['id'];?>" class="btn btn-xs btn-default ">
          <span class="glyphicon glyphicon-<?php echo (($product['featured']==1)?'minus':'plus');?>"></span></a>
          &nbsp <?php echo (($product['featured'] == 1)?'Featured Product':'');?>
        </td>
        <td><?php echo($product['sold']);?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php } include 'includes/footer.php'; ?>
